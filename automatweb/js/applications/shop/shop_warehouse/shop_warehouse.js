/*
 *  This jQuery wrapper is only required until we permanently include YUI!
*/
(function($) {
$(document).ready(function() {
  if (typeof(YUI) == "undefined") {
    $("body").addClass("yui3-skin-sam");
    $.getScript("http://yui.yahooapis.com/3.5.1/build/yui/yui-min.js", function(a, b, c) {
      YUI().use('panel', function (Y) {
        if (typeof(AW) == "undefined") {
          window.AW = {};
        }        
        AW.shop_warehouse = function(id) {
          var _id = id;
          return {
            delete_category: function(id) {
              new Y.Panel({
                contentBox: Y.Node.create('<div id="dialog" />'),
                bodyContent: '<div class="message">Vali kustutamisviis:</div>',
                centered: true,
                render: '.example',
                visible: true,
                buttons: {
                  footer: [ {
                      name: 'disconnect',
                      label: 'Eemalda kategooria peakategooria alt',
                      action: function (e) {
                        var dialog = this;
                        e.preventDefault();
                        $.ajax({
                          url: "orb.aw?class=shop_warehouse&action=remove_category",
                          data: { id: id, parent: get_property_data['cat'] },
                          complete: function () {
                            dialog._stackNode.remove();
                            reload_property(['product_management_toolbar', 'category_list', 'packets_list', 'product_management_list']);
                          }
                        });
                      }
                    }, {
                      name: 'delete',
                      label: 'Kustuta kategooria kataloogist',
                      action: function (e) {
                        var dialog = this;
                        e.preventDefault();
                        $.ajax({
                          url: "orb.aw?class=shop_warehouse&action=delete_category",
                          data: { id: id },
                          complete: function () {
                            dialog._stackNode.remove();
                            reload_property(['product_management_toolbar', 'category_list', 'packets_list', 'product_management_list']);
                          }
                        });
                      }
                    }, {
                      name: 'cancel',
                      label: 'Katkesta',
                      action: function (e) {
                        e.preventDefault();
                        this._stackNode.remove();
                      }
                    }
                  ]
                }
              });
            }
          };
        }
      });
    });
  }
});
})(jQuery);
