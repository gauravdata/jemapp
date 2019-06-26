
Event.observe(window, 'load', function(){
	Event.observe($('productGrid_massaction-select'), 'click', function() {
		if (this.value === 'translator') {
			var url = $('checkCron').value;

			new Ajax.Request(url, {
				method: 'POST',
				onSuccess: successFunc,
			});

			function successFunc(response){
				var result = JSON.parse(response.responseText);

				if (result.status == 1) {
					var type = 'warning';

					var html = '<ul class="messages"><li class="'+type+'-msg"><ul><li>' + result.msg + '</li></ul></li></ul>';
					$('messages').update(html);
					$('is_abort').value = 1;
				}
			}
		}
	});
});

varienGridMassaction.prototype.apply = function() {
	if(varienStringArray.count(this.checkedString) == 0) {
		alert(this.errorText);
		return;
	}

	var item = this.getSelectedItem();
	if(!item) {
		this.validator.validate();
		return;
	}

	/*Translator Check*/

	if ($('productGrid_massaction-select').value == 'translator' &&  $('is_abort').value == 1) {
		var url = $('checkCron').value;

		new Ajax.Request(url, {
			method: 'POST',
			onSuccess: successFunc,
		});

		function successFunc(response){
			var result = JSON.parse(response.responseText);

			if (result.status == 0) {
				$('is_abort').value = 0;
			}
		}
		if ($('productGrid_massaction-select').value == 'translator' &&  $('is_abort').value == 1) {
			if (confirm('Do you want to abort existing cron?') == false) {
				return;
			}
		}
	}
	/*end Translator Check*/

	this.currentItem = item;
	var fieldName = (item.field ? item.field : this.formFieldName);
	var fieldsHtml = '';

	if(this.currentItem.confirm && !window.confirm(this.currentItem.confirm)) {
		return;
	}

	this.formHiddens.update('');
	new Insertion.Bottom(this.formHiddens, this.fieldTemplate.evaluate({name: fieldName, value: this.checkedString}));
	new Insertion.Bottom(this.formHiddens, this.fieldTemplate.evaluate({name: 'massaction_prepare_key', value: fieldName}));

	if(!this.validator.validate()) {
		return;
	}

	if(this.useAjax && item.url) {
		new Ajax.Request(item.url, {
			'method': 'post',
			'parameters': this.form.serialize(true),
			'onComplete': this.onMassactionComplete.bind(this)
		});
	} else if(item.url) {
		this.form.action = item.url;
		this.form.submit();
	}
}