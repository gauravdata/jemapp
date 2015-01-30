var CAWCSTestConnection = Class.create({
    initialize: function (name) {
        window[name] = this;

        document.observe("dom:loaded", this.init.bind(this));
    },

    init: function () {
        this._button = $('awcsmtp_testconnection');
        if (this._button) {
            this._button.observe('click', this.submitInfo.bind(this));
            this._loadingMask = $('loading-mask');
            this._msgContainer = $$('.awcsmtp-message').first();
            this._msgContainer.hide();
        }
        this._ajaxTimeout = 30;
    },

    submitInfo: function () {
        var data = {
            host: $('customsmtp_smtp_host').value,
            port: $('customsmtp_smtp_port').value,
            user: $('customsmtp_smtp_login').value,
            pass: $('customsmtp_smtp_password').value,
            auth: $('customsmtp_smtp_auth').value,
            secure: $('customsmtp_smtp_ssl').value
        }

        this._loadingMask.show();

        this._request = new Ajax.Request(AW_CSMTP_CONFIG.testConnectionUrl, {
            parameters: data,
            onSuccess: function (response) {
                this._cancelAbortAjax();
                this._loadingMask.hide();
                try {
                    var resp = response.responseText.evalJSON();
                    if (typeof(resp.s) != 'undefined') {
                        if (resp.s) {
                            this.showSuccess(AW_CSMTP_CONFIG.msgSuccess);
                        } else {
                            this.showError((typeof(resp.msg) != 'undefined' && resp.msg) ? resp.msg : AW_CSMTP_CONFIG.msgFailure);
                        }
                    }
                } catch (ex) {
                    this.showError(AW_CSMTP_CONFIG.msgFailure);
                }
            }.bind(this),
            onFailure: function () {
                this._cancelAbortAjax();
                this._loadingMask.hide();
                this.showError(AW_CSMTP_CONFIG.msgFailure);
            }.bind(this)
        });

        this._timeoutId = setTimeout(this._abortAjax.bind(this), this._ajaxTimeout * 1000);
    },

    _cancelAbortAjax: function () {
        this._request = null;
        clearTimeout(this._timeoutId);
    },

    _abortAjax: function () {
        if (this._request) {
            this._request.transport.abort();
        }
    },

    showSuccess: function (msg) {
        this._msgContainer.removeClassName('awcsmtp-message-error');
        this._msgContainer.addClassName('awcsmtp-message-success');
        this._msgContainer.innerHTML = msg;
        this._msgContainer.show();
        setTimeout(function () {
            this._msgContainer.hide()
        }.bind(this), 5000);
    },

    showError: function (msg) {
        this._msgContainer.removeClassName('awcsmtp-message-success');
        this._msgContainer.addClassName('awcsmtp-message-error');
        this._msgContainer.innerHTML = msg;
        this._msgContainer.show();
        setTimeout(function () {
            this._msgContainer.hide()
        }.bind(this), 5000);
    }
});

new CAWCSTestConnection('AWCSTestConnection');