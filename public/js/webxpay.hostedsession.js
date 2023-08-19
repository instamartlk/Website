// webxpay hosted session
// version: 0.2

// validate dependencies
if (!typeof requirejs) {
    console.error("dependency error: requirejs is missing.");
}

function HostedSession() {
    successCallBack = function() {}; // these two variables override by generateSession function
    errorCallBack = function() {};

    function configure(card, readyCallBack) {
        PaymentSession.configure({
            fields: { card: card },
            frameEmbeddingMitigation: ["javascript"],
            callbacks: {
                initialized: function(response) {
                    readyCallBack(GenerateSession);
                },
                formSessionUpdate: function(response) {
                    if (response.status) {
                        if ("ok" == response.status) {
                            // check if the security code was provided by the user
                            if (
                                response.sourceOfFunds.provided.card
                                    .securityCode
                            ) {
                                successCallBack(response.session.id);
                            } else {
                                errorCallBack({
                                    type: "system_error",
                                    details: "cvv missing"
                                });
                            }
                        } else if ("fields_in_error" == response.status) {
                            errorCallBack({
                                type: "fields_in_error",
                                details: response.errors
                            });
                        } else if ("request_timeout" == response.status) {
                            errorCallBack({
                                type: "request_timeout",
                                details: response.errors.message
                            });
                        } else if ("system_error" == response.status) {
                            errorCallBack({
                                type: "system_error",
                                details: response.errors.message
                            });
                        }
                    } else {
                        errorCallBack({
                            type: "fields_in_error",
                            details: response
                        });
                    }
                }
            },
            interaction: {
                displayControl: {
                    formatCard: "EMBOSSED",
                    invalidFieldCharacters: "REJECT"
                }
            }
        });
    }

    function GenerateSession(callback, error) {

        // initiate cards basic verification and create a session
        successCallBack = callback;
        errorCallBack = error;

        PaymentSession.updateSessionFromForm("card");
    }

    return {
        configure: configure,
    };
}

function WebxpayTokenizeInit(data) {
    console.log(data);
    var card = data.card;
    var readyCallBack = data.ready;
    var credentials = data.credentials;

    const WXP_DEPENDENCIES = [
        `https://ap-gateway.mastercard.com/form/version/${credentials.version}/merchant/${credentials.id}/session.js`
    ];

    requirejs(WXP_DEPENDENCIES, function() {
        var hostedSession = new HostedSession();
        hostedSession.configure(card, readyCallBack);
    });
}
