(function($){

window.onload = (event) =>{

	$("#print-text-input").val('N/A')
	$( '#print-selector' ).val('Not printed')

};

$('#print-selector').change(function(e){
	var id = $('#akb-new-price').data("id");
	WprDoAjax(id);
	});

function WprDoAjax(id){
	var faces = $( '#print-selector' ).val();
	data = {
		action: 'my_actionz',
		security : MyAjax.security,
		id: id,
		faces: faces,
	  };
	  $.get(MyAjax.ajaxurl, data, function(response) {
		
		console.log( id );
		console.log( faces );
		console.log( response );
		var json = JSON.parse(response);
		$( '#akb-new-price' ).empty();
		var content="";

		content +=json[0].price;

		jQuery( "#akb-new-price" ).html( content );

				
	  });

			}



$('#print-selector').on('change',function(){
	if( $(this).val()==='Not printed'){
	$("#print-text-row").hide()
	$("#print-text-input").val('N/A')
	}
	else{
	$("#print-text-row").show()
	$("#print-text-input").val('')
	}
});
let textArea = document.getElementById("print-text-input");
let characterCounter = document.getElementById("char_count");
const maxNumOfChars = 75;

const countCharacters = () => {
// let numOfEnteredChars = textArea.value.length;
// let counter = maxNumOfChars - numOfEnteredChars;
counter = textArea.value.length;
characterCounter.textContent = counter + "/75";
if (counter <= 10) {
	characterCounter.style.color = "green";
	characterCounter.style.fontWeight = "700";
} else if (counter < 25) {
	characterCounter.style.color = "orange";
	characterCounter.style.fontWeight = "800";
} else if (counter < 50) {
	characterCounter.style.color = "red";
	characterCounter.style.fontWeight = "900";
} else {
	characterCounter.style.color = "black";
	characterCounter.style.fontWeight = "900";
}
};
textArea.addEventListener("input", countCharacters);

})(jQuery);