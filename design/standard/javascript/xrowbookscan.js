jQuery(document).ready(function($) {
    if( $('.xbs_showpreview').length ){
        $('.xbs_showpreview').each(function(){
            $(this).click(function(e){
                var id = $(this).attr('data-pid'),
                    contid = $(this).attr('data-contid'),
                    params = '::'+id+'::'+contid;
                if(typeof $(this).attr('data-href') != 'undefined') {
                    var href = $(this).attr('data-href');
                    params += '::'+href;
                }
                $.ez('xrowbookscan::viewPage::'+params, false, function(data) {
                    if(typeof data != "undefined") {
                        $('body').append(data.content);
                        var left = e.pageX+50,
                            top = e.pageY-250,
                            popup_container_id = '#xbs_popup_'+contid;
                        if( left < 0)
                            left = 0;
                        if( top < 0)
                            top = 0;
                        $('.xbs_popup_container').hide();
                        if( $(popup_container_id).length ){
                            $(popup_container_id).show().css({'top': top, 'left': left});
                            $('.xbs_popup_close').click(function(){
                                $('.xbs_popup_container').hide();
                            });
                        }
                    }
                });
            });
        });
    }
});