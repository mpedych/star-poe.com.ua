jQuery(document).ready(function($) {
    var input = document.querySelector("#reg_phone");
    var iti = window.intlTelInput(input, {
		onlyCountries: ["ua"],
		initialCountry: "ua",
        utilsScript: "//cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
		
	
    }
	);
	

	const errorMsg = document.querySelector("#error-msg");

	// here, the index maps to the error code returned from getValidationError - see readme
	const errorMap = ["Невірний номер", "Неправильний код країни", "Занадто короткий", "Занадто довгий", "Невірний номер"];

	const reset = () => {
	  input.classList.remove("error");
	  errorMsg.innerHTML = "";
	  errorMsg.classList.add("hide");
	};

	// on blur: validate
	input.addEventListener('blur', () => {
	  reset();
	  if (input.value.trim()) {
		if (iti.isValidNumber()) {
		 
		} else {
		  input.classList.add("error");
		  const errorCode = iti.getValidationError();
		  errorMsg.innerHTML = errorMap[errorCode];
		  $('#reg_phone').val('');
		  errorMsg.classList.remove("hide");
		}
	  }
	});

	// on keyup / change flag: reset
	input.addEventListener('change', reset);
	input.addEventListener('keyup', reset);
    $("#reg_phone").on("countrychange", function() {
        var countryData = iti.getSelectedCountryData();
        var dialCode = countryData.dialCode;
        $(this).val("+" + dialCode + " ");
    });
}
					  
					  );
jQuery(document).ready(function($) {
	var billingInput = document.querySelector("#billing_phone");
var billingIti = window.intlTelInput(billingInput, {
    onlyCountries: ["ua"],
	initialCountry: "ua",
    utilsScript: "//cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
});

});
