<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <main>
            <style type="text/css">
            .btn-circle.btn-lg {
                width: 50px;
                height: 25px;
                padding: 2px 2px;
                font-size: 10px;
                line-height: 1.33;
                border-radius: 25px;
            }

            .btn-record.btn-lg {
                width: 50px;
                height: 25px;
                padding: 2px 2px;
                font-size: 10px;
                line-height: 1.33;
                border-radius: 5px;
            }

            .btn-record-recording {
                background-color: #B63737;
                border-color: #B63737;
                color: #fff;
            }

            </style>
            <div>
                <span id="amazonConnectLoginMessage">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="amazonConnectLoginMessage" class="alert alert-danger" role="alert">
                                Please login to <a onclick="self.close()" href="https://siklab.my.connect.aws/ccp-v2/" target="_blank">Amazon Connect</a> then refresh the page.
                            </div>
                        </div>
                    </div>
                </span>
                <div id="container-div-amazon-connect" style="width: 320px !important; height: 520px !important; display: none;"></div>
                <hr class="hr-line-solid" style="border-color:gray">
                <div>
                    <div style="float:left;">
                        <div style="width: 250px !important; height: 200px !important;">
                            <div class="p-sm">
                                <div style="height:200px">
                                    <div class="full-height-scroll" id="ccpDisplayStatus">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="float:left;">
                        <center>
                            <div class="m-t-sm">
                                <button id="recordStatus" type="button" class="btn btn-record btn-lg"><i class="fa fa-circle" aria-hidden="true"></i> REC</button>
                            </div>
                            <div style="margin-top:50px">
                                <button title="Pause Recording" disabled id="pauseRecordingBtn" type="button" class="btn btn-warning btn-circle btn-lg"><i class="fa fa-pause" aria-hidden="true"></i></button>
                            </div>
                            <div class="m-t-sm">
                                <button title="Resume Recording" disabled id="resumeRecordingBtn" type="button" class="btn btn-success btn-circle btn-lg" style="background-color:#5cb85c;border-color: #4cae4c;"><i class="fa fa-play" aria-hidden="true"></i></button>
                            </div>
                        </center>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </main>
    </div>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/connect-streams-min.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
    <script type="text/javascript">
    var noInformationAvailableTemplate = '<p>No Contact Information Available</p>';

    var isInit = false;

    var currentContactId = null;

    var containerDiv = document.getElementById("container-div-amazon-connect");
    var instanceURL = 'https://siklab.my.connect.aws/ccp-v2/softphone';

    window.myCPP = window.myCPP || {};

    // initialize the streams api
    function amazon_connect_init() {
        // initialize the ccp
        connect.core.initCCP(containerDiv, {
            ccpUrl: instanceURL, // REQUIRED
            loginPopup: true, // optional, defaults to `true`
            loginPopupAutoClose: true, // optional, defaults to `false`
            loginOptions: { // optional, if provided opens login in new window
                autoClose: true, // optional, defaults to `false`
                height: 600, // optional, defaults to 578
                width: 400, // optional, defaults to 433
                top: 0, // optional, defaults to 0
                left: 0 // optional, defaults to 0
            },
            region: 'us-east-1', // REQUIRED for `CHAT`, optional otherwise
            softphone: { // optional, defaults below apply if not provided
                allowFramedSoftphone: true, // optional, defaults to false
                disableRingtone: false, // optional, defaults to false
                ringtoneUrl: "/ringtones/home-ringtone-4438.mp3" // optional, defaults to CCP’s default ringtone if a falsy value is set
            },
            pageOptions: { //optional
                enableAudioDeviceSettings: true, //optional, defaults to 'false'
                enablePhoneTypeSettings: true //optional, defaults to 'true'
            },
            ccpAckTimeout: 5000, //optional, defaults to 3000 (ms)
            ccpSynTimeout: 3000, //optional, defaults to 1000 (ms)
            ccpLoadTimeout: 50000 //optional, defaults to 5000 (ms)
        });

        /*
         *
         * This works when user is loggedin
         *
         */
        connect.agent(subscribeToAgentEvents);
        connect.contact(subscribeContactEvents);

        function subscribeToAgentEvents(agent) {
            // var state = agent.getState();
            // console.log(state);

            agent.onRefresh(function(agent) {
                console.log('Agent Events onRefresh');
                console.log(agent);
                document.getElementById("amazonConnectLoginMessage").innerHTML = '';
                document.getElementById("container-div-amazon-connect").style.display = "block";
            });

            /*
             * Set user change status i.e. from Break To Available
             */
            agent.onStateChange(function(agentStateChange) {
                console.log('Agent Events onStateChange');
                console.log(agentStateChange);
                stateChange(agentStateChange.oldState, agentStateChange.newState);
            });

            /*
             * Set status to Offline
             */
            agent.onOffline(function(agent) {
                console.log('Agent Events onOffline');
                console.log(agent);
                stateChange(agentStateChange.oldState, agentStateChange.newState);
            });
        }

        /*
         * When user logout
         */
        const eventBus = connect.core.getEventBus();
        eventBus.subscribe(connect.EventType.TERMINATED, () => {
            logout();
        });

        function subscribeContactEvents(contact) {
            var attributeMap = contact.getAttributes();
            console.log("Contact getAttributes");
            console.log(attributeMap);

            contact.onRefresh(function(contact) {
                // always triggered as CCP automatically refresh
                console.log("Contact Events onRefresh");
                console.log(contact);
            });
            contact.onIncoming(function(contact) {
                console.log("Contact Events onIncoming");
                console.log(contact);

                currentContactId = contact.contactId;
            });
            contact.onPending(function(contact) {
                console.log("Contact Events onPending");
            });
            contact.onConnecting(function(contact) {
                // Call is ringing
                console.log("Contact Events onConnecting");
                console.log(contact);

                displayStatus(contact.contactId);

                currentContactId = contact.contactId;
            });
            contact.onAccepted(function(contact) {
                // Call Accepted or answered
                console.log("Contact Events onAccepted");
                console.log(contact);

                // redirectToApplication(contact.contactId);
                // Problem with miss call and opening the lead, better when they talk or connected
                // openNewWindow(contact.contactId);

                currentContactId = contact.contactId;

                // $('#stopRecordingBtn').prop("disabled", false);
                $('#pauseRecordingBtn').prop("disabled", false);
                $('#resumeRecordingBtn').prop("disabled", true);

                $("#recordStatus").addClass('btn-record-recording');
            });
            contact.onMissed(function(contact) {
                // Call was missed, didnt answer by agent and caller drop the call
                console.log("Contact Events onMissed");
                console.log(contact);

                // contactOnMissed(contact.contactId);

                currentContactId = null;
            });
            contact.onEnded(function(contact) {
                // Call ended
                console.log("Contact Events onEnded");
                console.log(contact);

                // contactOnEnded(contact.contactId);
                $('#ccpDisplayStatus').html(noInformationAvailableTemplate);
                //();

                currentContactId = null;

                // $('#stopRecordingBtn').prop("disabled", true);
                $('#pauseRecordingBtn').prop("disabled", true);
                $('#resumeRecordingBtn').prop("disabled", true);

                $("#recordStatus").removeClass('btn-record-recording');
            });
            contact.onDestroy(function(contact) {
                console.log("Contact Events onDestroy");
                console.log(contact);

                currentContactId = null;

                // $('#stopRecordingBtn').prop("disabled", true);
                $('#pauseRecordingBtn').prop("disabled", true);
                $('#resumeRecordingBtn').prop("disabled", true);

                $("#recordStatus").removeClass('btn-record-recording');
            });
            contact.onACW(function(contact) {
                // Call After Call Work
                console.log("Contact Events onACW");
                console.log(contact);

                $('#ccpDisplayStatus').html(noInformationAvailableTemplate);
                //resizeMe();

                currentContactId = null;

                // $('#stopRecordingBtn').prop("disabled", true);
                $('#pauseRecordingBtn').prop("disabled", true);
                $('#resumeRecordingBtn').prop("disabled", true);

                $("#recordStatus").removeClass('btn-record-recording');
            });
            contact.onConnected(function(contact) {
                // Call Connected
                console.log("Contact Events onConnected");
                console.log(contact);
                openNewWindow(contact.contactId);

                currentContactId = contact.contactId;

                // $('#stopRecordingBtn').prop("disabled", false);
                $('#pauseRecordingBtn').prop("disabled", false);
                $('#resumeRecordingBtn').prop("disabled", true);

                $("#recordStatus").addClass('btn-record-recording');
            });
            contact.onError(function(contact) {
                console.log("Contact Events onError");
                console.log(contact);

                currentContactId = null;
            });
        }
    }

    function stateChange(oldState, newState) {
        // Everytime the CCP is closed, it always calls the state change and trigger init.
        // We need to catch every init so CRM user wont refresh, we only refresh on first init as first action is to login
        if (!isInit) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('ajaxAgentInit') }}",
                type: 'POST',
                dataType: 'json',
                success: function(jObj) {
                    if (jObj.successful) {
                        isInit = true;

                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ url('ajaxAgentStateChange') }}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                oldState: oldState,
                                newState: newState
                            },
                            success: function(jObj) {
                                if (jObj.successful) {
                                    if (window.opener != null && window.opener.location != null) {
                                        window.opener.location.reload();
                                    }
                                }
                            }
                        });
                    }
                }
            });
        }

        // Offline
        // Training
        // Available
        // Meeting
        // Break
        // CallingCustomer
        // Busy
        if (newState == "Offline" ||
            newState == "Training" ||
            newState == "Available" ||
            newState == "Meeting" ||
            newState == "Break") {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('ajaxAgentStateChange') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    oldState: oldState,
                    newState: newState
                },
                success: function(jObj) {
                    if (jObj.successful) {
                        if (window.opener != null && window.opener.location != null) {
                            window.opener.location.reload();
                        }
                    }
                }
            });
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('ajaxAgentUpdateCurrentStatus') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                newState: newState
            }
        });
    }

    function logout() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('ajaxAgentStatusLogout') }}",
            type: 'POST',
            dataType: 'json',
            success: function(jObj) {
                if (jObj.successful) {
                    if (window.opener != null && window.opener.location != null) {
                        window.opener.location.reload();
                    }

                    //close self as well
                    window.close();
                }
            }
        });
    }

    function openNewWindow(contactId) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('ajaxOpenNewWindow') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                contactId: contactId
            },
            success: function(jObj) {
                if (jObj.successful) {
                    //window.open(jObj.url, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, width=" + 320 + ", height=" + 1024);
                    window.open(jObj.url, "_blank");
                }
            }
        });
    }

    function displayStatus(contactId) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('ajaxDisplayStatus') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                contactId: contactId
            },
            success: function(jObj) {
                if (jObj.successful) {
                    $('#ccpDisplayStatus').html(jObj.html);
                } else {
                    $('#ccpDisplayStatus').html(noInformationAvailableTemplate);
                }
                //resizeMe();
            }
        });
    }

    function resizeMe() {
        height = document.getElementById("container-div-amazon-connect").offsetHeight + document.getElementById("ccpDisplayStatus").offsetHeight;
        width = document.getElementById("mainBodyDiv").offsetWidth;
        self.resizeTo(width, height + 650);
    }

    $(document).ready(function() {
        amazon_connect_init();
        $('iframe').attr("frameBorder", "0");

        $('#ccpDisplayStatus').slimscroll({
            height: '100%',
            alwaysVisible: true,
            railVisible: true
        });

        resizeMe();

        $('#pauseRecordingBtn').click(function() {
            $('#stopRecordingBtn').prop("disabled", true);
            $('#pauseRecordingBtn').prop("disabled", true);
            $('#resumeRecordingBtn').prop("disabled", false);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('ajaxRecordingPause') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    contactId: currentContactId
                },
                success: function(jObj) {
                    if (jObj.successful) {
                        $("#recordStatus").removeClass('btn-record-recording');
                    }
                }
            });
        });

        $('#resumeRecordingBtn').click(function() {
            $('#stopRecordingBtn').prop("disabled", false);
            $('#pauseRecordingBtn').prop("disabled", false);
            $('#resumeRecordingBtn').prop("disabled", true);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('ajaxRecordingResume') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    contactId: currentContactId,
                    csrfmhub: $('#csrfheaderid').val()
                },
                success: function(jObj) {
                    if (jObj.successful) {
                        $("#recordStatus").addClass('btn-record-recording');
                    }
                }
            });
        });
    });

    </script>
</body>

</html>
