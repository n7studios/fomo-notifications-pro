var wpzinc_deactivation_url;jQuery(document).ready((function($){$("span.deactivate a").on("click",(function(a){var i=$(this).closest("tr").data("slug");return void 0===i||(i!=wpzinc_dashboard.plugin.name||(a.preventDefault(),wpzinc_deactivation_url=$(this).attr("href"),$("#wpzinc-deactivation-modal").css({top:$(this).offset().top-$(this).height()-25+"px",left:$(this).offset().left+$(this).width()+20+"px"}),void $("#wpzinc-deactivation-modal, #wpzinc-deactivation-modal-overlay").show()))})),$('input[name="wpzinc-deactivation-reason"]').on("change",(function(a){$('input[name="wpzinc-deactivation-reason-text"]').attr("placeholder",$(this).data("placeholder")).show(),$('input[name="wpzinc-deactivation-reason-email"]').show(),$("small.wpzinc-deactivation-reason-email").css("display","block")})),$("form#wpzinc-deactivation-modal-form").on("submit",(function(a){a.preventDefault();var i=$("input[name=wpzinc-deactivation-reason]:checked",$(this)).val(),n=$("input[name=wpzinc-deactivation-reason-text]",$(this)).val(),t=$("input[name=wpzinc-deactivation-reason-email]",$(this)).val();void 0!==i&&$.ajax({url:ajaxurl,type:"POST",async:!0,data:{action:"wpzinc_dashboard_deactivation_modal_submit",product:wpzinc_dashboard.plugin.name,version:wpzinc_dashboard.plugin.version,reason:i,reason_text:n,reason_email:t},error:function(a,i,n){},success:function(a){}}),$("#wpzinc-deactivation-modal, #wpzinc-deactivation-modal-overlay").hide(),window.location.href=wpzinc_deactivation_url})),$("#wpzinc-deactivation-modal-overlay").on("click",(function(a){$("#wpzinc-deactivation-modal, #wpzinc-deactivation-modal-overlay").hide()}))}));