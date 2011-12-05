#!/usr/bin/php
<?php
/**
 * TextMate command to parse the output from PHPCodeSniffer.
 *
 * @category  TextMate Bundles
 * @package   PHPCS TMBundle
 * @author    Mat Gadd <mgadd@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

if (isset($_SERVER['TM_FILEPATH'])) {
	$fileName = $_SERVER['TM_FILEPATH'];
} else {
	throw new Exception('Missing file path');
}

require_once('PHPCSHelper.php');
require_once('PHPCSView.php');

$cs = new PHPCSHelper($fileName);
$cs->setStandard('Namesco');
$valid = $cs->validate();

$view = new PHPCSView('output.xhtml');

?>
<!DOCTYPE html>
<html>
<head>
	<title>CodeSniffer, <?php echo $fileName; ?></title>
	
    <style type="text/css">
    body {
        background-color: #ffffff;
        font-family: Helvetica, sans-serif;
        font-size: 12px;
    }
    a, a:visited {
        text-decoration: none;
    }
    a:hover {
        color: black;
    }

    .error {
      cursor: pointer;
        border: 2px solid red;
        background-color: #fff2f2;
        width: 95%;
        color: red;
        margin-bottom: 7px;
        padding: 3px;
    }

    .error.over {
        background-color: #C64949;
        color: #ffffff;
    }

    .warning {
        border: 2px solid #9C8023;
        background-color: #feffc2;
        color: black;
        width:95%;
        margin-bottom: 7px;
        padding: 3px;
    }

    .warning.over {
      background-color: #9C8023;
      color: #ffffff;
    }
    .success {
      background: #e6efc2;
      color: #264409;
      border-color: #c6d880;
      padding: 0.8em;
      margin-bottom: 1em;
      border-style: solid;
      border-width: 2px;
    }
    .type {
        font-weight: bold;
    }
    .error-msg {
        margin-top: 2px;
    }
    .summary {
        width: 80%;
        margin-bottom: 3px;
        padding-bottom: 3px;
        color: black;
    }
    .footer {
        border-top: 1px solid black;
        width: 95%;
        margin-top: 3px;
        padding-top: 3px;
        color: black;
    }
    </style>
    <script type="text/javascript">
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
            var locked       = false;
            eElem.onmouseover = function() {
              if (locked === false) {
                eElem.className = classN + ' over';
              }
            };

            eElem.onmouseout = function() {
              if (locked === false) {
                eElem.className = classN;
              }
            };

            eElem.onclick = function() {
              if (locked === true) {
                eElem.className = classN;
              }

              // locked = !locked;
              window.location = textMateLink;
            };
          }) (j, types[i].className, types[i].classPrefix);
        }//end for types[i].count
      }//end for typesLen
    }

    </script>
</head>
<body onload="init();">
	<div class="summary">PHP CodeSniffer Results</div>
<?php
	if ((bool) $valid) {
		echo '<div class="success">No coding standard violations found.</div>', PHP_EOL;
	} else {
		echo implode(PHP_EOL, $cs->getViolations()), PHP_EOL;
	}
?>
	<div class="footer">
		Errors: <?php echo $cs->getErrorCount(); ?>.<br />
		Warnings: <?php echo $cs->getWarningCount(); ?>.
	</div>
</body>
</html>