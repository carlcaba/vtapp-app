/**
 *  Form Wizard
 */

'use strict';

$(function () {
  const select2 = $('.select2'),
    selectPicker = $('.selectpicker');

  // Bootstrap select
  if (selectPicker.length) {
    selectPicker.selectpicker();
  }

  // select2
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>');
      $this.select2({
        placeholder: 'Seleccione un valor',
        dropdownParent: $this.parent()
      });
    });
  }
});

(function () {
	// Vertical Icons Wizard
	// --------------------------------------------------------------------
	const wizardIconsVertical = document.querySelector('.wizard-vertical-icons-example');

	if (typeof wizardIconsVertical !== undefined && wizardIconsVertical !== null) {
		const 	wizardIconsVerticalBtnNextList = [].slice.call(wizardIconsVertical.querySelectorAll('.btn-next')),
				wizardIconsVerticalBtnPrevList = [].slice.call(wizardIconsVertical.querySelectorAll('.btn-prev')),
				wizardIconsVerticalBtnSubmit = wizardIconsVertical.querySelector('.btn-submit');

		const verticalIconsStepper = new Stepper(wizardIconsVertical, {
				linear: false
			}
		);

		if (wizardIconsVerticalBtnNextList) {
			wizardIconsVerticalBtnNextList.forEach(wizardIconsVerticalBtnNext => {
				wizardIconsVerticalBtnNext.addEventListener('click', event => {
					verticalIconsStepper.next();
				});
			});
		}
		if (wizardIconsVerticalBtnPrevList) {
			wizardIconsVerticalBtnPrevList.forEach(wizardIconsVerticalBtnPrev => {
				wizardIconsVerticalBtnPrev.addEventListener('click', event => {
					verticalIconsStepper.previous();
				});
			});
		}
		if (wizardIconsVerticalBtnSubmit) {
			wizardIconsVerticalBtnSubmit.addEventListener('click', event => {
				alert('Submitted..!!');
			});
		}
	}
})();
