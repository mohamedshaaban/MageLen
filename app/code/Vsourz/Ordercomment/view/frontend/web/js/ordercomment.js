/**
 *
 */
define(
    [
        'jquery',
        "jquery/ui"
    ],
    function ($) {
        'use strict';
        console.log("Loaded : Ordercomment js");

        $(document.body).on('click', '.order-comment-btn', function () {

            var flag = true;

            if (document.getElementById("order_comments_required")) {
               var order_comments_required = document.getElementById("order_comments_required").value;
            }

            if (order_for_required = document.getElementById("order_for_required")) {
               var order_for_required = $("#order_for_required").val();
            }

            if (order_comments_required == "") {
                document.getElementById('order_comments_required_error').innerHTML = 'This is a required field.';
                var flag = false;
            }

            if (order_for_required == "") {
                document.getElementById('order_for_required_error').innerHTML = 'This is a required field.';
                var flag = false;
            }

            if (flag == false) {
                return false;
            } else {
                return true;
            }
       });
    }
)