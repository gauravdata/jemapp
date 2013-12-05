var AWHDUDepartment = Class.create({
    initialize:function (name) {
        window[name] = this;
        document.observe('dom:loaded', this.init.bind(this));
    },

    init:function () {
        this.selectUseNotifications = $('notify');
        this.inputEmail = $('contact');
        this.starSpan = $$('#edit_form label[for="contact"]').length ? $$('#edit_form label[for="contact"] span').first() : null;
        console.log(this.selectUseNotifications, this.inputEmail);
        if (this.selectUseNotifications && this.inputEmail) {
            this.selectUseNotifications.observe('change', this.checkUseNotifications.bind(this));
            this.checkUseNotifications();
        }
    },

    checkUseNotifications:function () {
        if (parseInt(this.selectUseNotifications.value)) {
            this.makeEmailRequired();
        } else {
            this.unrequireEmailField();
        }
    },

    makeEmailRequired:function () {
        this.inputEmail.addClassName('required-entry');
        this.inputEmail.addClassName('validate-uniq-email');
        if (this.starSpan) {
            this.starSpan.show();
        }
    },

    unrequireEmailField:function () {
        this.inputEmail.removeClassName('required-entry');
        this.inputEmail.removeClassName('validate-uniq-email');
        if (this.starSpan) {
            this.starSpan.hide();
        }
    }
});

new AWHDUDepartment('awhdudepartment');