/**
 * Created by andrew on 22/05/2017.
 */
function removeParam(elm) {
  $(elm).parents('.parameter').remove();
}

function refreshValues(elm) {
  if ($(elm).val() === 'BETWEEN') {
    // Display the two text fields.
    $(elm).parent().parent('.row').find('.single-value').find('input').val('');
    $(elm).parent().parent('.row').find('.dual-value').show();
    $(elm).parent().parent('.row').find('.dual-value').css("display", "inline-block");
    $(elm).parent().parent('.row').find('.single-value').hide();
  }
  else {
    // Display the single text field
    $(elm).parent().parent('.row').find('.dual-value').find('input').val('');
    $(elm).parent().parent('.row').find('.dual-value').hide();
    $(elm).parent().parent('.row').find('.single-value').show();
    $(elm).parent().parent('.row').find('.single-value').css("display", "inline-block");
  }
}