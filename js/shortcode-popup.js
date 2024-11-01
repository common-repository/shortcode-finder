/*global  jQuery, mssc, tinyMCE*/
/*jslint browser: true continue: true*/
(function ($) {
    'use strict';

    mssc.popupIframeLoad = function (that) {
		var el  = {
				select: $("#mssc-select-wrapper"),
				attr: $("#mssc-attr-wrapper"),
				submit: $("#mssc-submit")
		    },
		    sc  = $(that).get(0).contentWindow.shortcodefinder,
            str = '',
            astr = '',
            i,
            jqe  = function (x, n) {
				astr += '<li><label><input type="checkbox" name="' + sc[i].name + '-' + n + '" value="' + n + '"><span>' + n + '</span></label></li>';
            },
		    ln;

		for (i = 0, ln = sc.length; i < ln; i += 1) {
            str += '<li><label><input type="radio" name="mssc-shortcode-select" value="' + sc[i].name + '"><span>' + sc[i].name + '</span></label></li>';
            
            astr += '<div id="mssc-' + sc[i].name + '-attr" class="mssc-attr"><ul>';
            
            if (sc[i].params.length === 0) {
				astr += '<li><label>No Attributes</label></li></ul></div>';
				continue;
			}

			$.each(sc[i].params, jqe);

			astr += '</ul></div>';
        }
        
        el.select.find('ul').html(str);
        el.attr.append(astr);

		// Shortcode Selected
		el.select.off('change').on('change', ':radio', function () {
	
			// Hide all Attributes
			el.attr.find("div").removeClass('mssc-attr-show');
			
			// Show Attributes
			$("#mssc-" + $(this).val() + "-attr").addClass('mssc-attr-show');

			//Reset all checks
			el.attr.find(":checkbox").prop('checked', false);
					
			// Enable Button
			el.submit.removeAttr('disabled');
			
		});

		el.submit.off('click').click(function (event) {
			var frmvals = $("#mssc-post-form").serializeArray(),
			    $cnt    = $('#content'),
			    str     = '',
			    i;

			for (i = frmvals.length - 1; i > 0; i -= 1) {
				str += ' ' + frmvals[i].value + '=""';
			}

			str = '[' + frmvals[0].value + str + '][/' + frmvals[0].value + ']';

			//If visual editor is not active
			function plainText() {
				var cursorPos = $cnt[0].selectionStart,
				    val       = $cnt.val();

				$cnt.val(val.substring(0, cursorPos) + str + val.substring(cursorPos));
			}

			//If visual editor is active
			if (typeof (tinyMCE) !== "undefined") {
				if (tinyMCE.activeEditor === null || tinyMCE.activeEditor.isHidden()) {
					plainText();
				} else {
                    tinyMCE.execCommand('mceInsertContent', false, str);
                }
			} else {
                plainText();
            }

			//Close ThickBox
			$('#TB_closeWindowButton').click();

			event.preventDefault();
			return false;
		});

	};
}(jQuery));