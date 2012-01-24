var errors   = <?php echo $cs->getErrorCount(); ?>;
var warnings = <?php echo $cs->getWarningCount(); ?>;

function init() {
  var types = [
    {
      className: 'error',
      classPrefix: 'e',
      count: errors
    },
    {
      className: 'warning',
      classPrefix: 'w',
      count: warnings
    }
  ];

  var typesLen = types.length;

  for (var i = 0; i < typesLen; i++) {
    for (var j = 1; j <= types[i].count; j++) {
      (function(idx, classN, classP) {
        var id           = classP + idx;
        var eElem        = document.getElementById(id);
        var textMateLink = eElem.getAttribute('txmt');

        eElem.onclick = function() {
          window.location = textMateLink;
        };
      }) (j, types[i].className, types[i].classPrefix);
    } //end for types[i].count
  } //end for typesLen
}

function goto_url(url) {
	window.location = url;
}