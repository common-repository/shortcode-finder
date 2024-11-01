/*global  jQuery*/
/*jslint browser: true*/
(function ($) {
    'use strict';
    
	var mssc = window.mssc = window.mssc || {};
    
    //JS implementation of sort function
    mssc.sort_shortcodes_by_function = function (sc) {
        var sca = {
                "Native WordPress": [],
                "Misc": []
            },
            ln  = sc.length,
            i,
            sdn;

        for (i = 0; i < ln; i += 1) {
            switch (sc[i].type) {
            case 'native':
                sca["Native WordPress"].push(sc[i]);
                break;
            case 'plugin':
            case 'theme':
                sdn = sc[i].details.Name;
                
                if (!sdn) {
                    sdn = sc[i].name;
                }

                if (sca[sdn]) {
                    sca[sdn].push(sc[i]);
                } else {
                    sca[sdn] = [sc[i]];
                }
                break;
            default:
                sca.Misc.push(sc[i]);
                break;
            }
        }

        return sca;
    };

    //For Tools Page
    mssc.toolsIframeLoad = function (that) {
        var el  = {
				ut: $("#userShortcodes")
		    },
		    sca = mssc.sort_shortcodes_by_function($(that).get(0).contentWindow.shortcodefinder),
            str = '',
            jqe = function (k, v) {
                if (k === "0") {
                    str += v;
                } else {
                    str += ', ' + v;
                }
            },
            ln,
            i;
        
        $.each(sca, function (k, v) {
            ln  = v.length;
            
            if (ln > 0) {
                str += '<h3>' + k + '</h3>';
                str += '<table class="widefat importers"><tbody><tr><th><strong>Shortcode</strong></th><th><strong>Arguments</strong></th></tr>';

                for (i = 0; i < ln; i += 1) {
                    str += '<tr><th>[' + v[i].name + ']</th>';
                    
                    if (v[i].params.length > 0) {
                        str += '<td>' + v[i].params.join(', ') + '</td></tr>';
                    } else if (!$.isArray(v[i].params)) {
                        str += '<td>';
                        $.each(v[i].params, jqe);
                        str += '</td></tr>';
                    } else {
                        str += '<td>N/A</td></tr>';
                    }
                }

                str += '</tbody></table>';
            }
        });

        el.ut.html(str);
    };
}(jQuery));