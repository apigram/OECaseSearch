/**
 * Created by andrew on 22/05/2017.
 */
function removeParam(elm) {
  $(elm).parent('.parameter').remove();
}

function refreshValues(elm) {
  if ($(elm).val() === 'BETWEEN') {
    // Display the two text fields.
    $(elm).parent('div').children('.single-value').find('input').val('');
    $(elm).parent('div').children('.dual-value').show();
    $(elm).parent('div').children('.dual-value').css("display", "inline-block");
    $(elm).parent('div').children('.single-value').hide();

  }
  else {
    // Display the single text field
    $(elm).parent('div').children('.dual-value').find('input').val('');
    $(elm).parent('div').children('.dual-value').hide();
    $(elm).parent('div').children('.single-value').show();
    $(elm).parent('div').children('.single-value').css("display", "inline-block");
  }
}

/**
 * Show/hide the supplied target div.
 * @param link The link that was clicked.
 * @param target The class/ID of the detail div.
 */
function toggleDetail(link, target) {
  $(link).parent().parent().find(target).toggle();
  if ($(link).text().search('Show') !== -1) {
    var text = $(link).text().replace('Show', 'Hide');
    $(link).text(text);
  }
  else if ($(link).text().search('Hide') !== -1) {
    var text = $(link).text().replace('Hide', 'Show');
    $(link).text(text);
  }
}
