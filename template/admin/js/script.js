/*
* @Author: th_le
* @Date:   2017-06-19 09:32:41
* @Last Modified by:   th_le
* @Last Modified time: 2017-06-19 14:23:05
*/

'use strict';

$(document).ready(function() {
  $.extend($.fn.dataTable.defaults, {
    autoWidth: true,
    dom: '<"datatable-scroll table-responsive clearfix"tr><"datatable-footer clearfix"ip>',
    language: {
      paginate: {
        'first': 'First',
        'last': 'Last',
        'next': '&rarr;',
        'previous': '&larr;'
      },
      processing: '<div class="box-loading"><div class="cssload"><span></span></div></div>'
    }
  });
});
