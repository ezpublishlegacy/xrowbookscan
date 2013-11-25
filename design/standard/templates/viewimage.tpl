{def $divider = ezini( 'Settings', 'ShowImageDivider', 'xrowbookscan.ini' )
     $image_width = $image.width|div( $divider )
     $image_height = $image.height|div( $divider )
     $width = $image_width
     $height = $image_height|sum( 50 )}
<div style="height:{$height}px; width: {$width}px; display: none;" id="xbs_popup_{$contid}" class="xbs_popup_container">
    <span class="xbs_popup_close">X</span>
    <div class="xbs_popcontent">
        <div class="xbs_zoomimageminus">-</div><div class="xbs_zoomimageplus">+</div>
        <img src={$image.url|ezurl()} style="width: {$image_width}px; height: {$image_height}px" class="xbs_zoomimage" />
    </div>
</div>
<script type="text/javascript">
{literal}
if ($('.xbs_zoomimage').length) {
    var divider = {/literal}{$divider}{literal},
        original_width = {/literal}{$image.width}{literal},
        width = {/literal}{$image_width}{literal},
        height = {/literal}{$image_height}{literal},
        cont_height = {/literal}{$height}{literal},
        zoom_width = width,
        zoom_height = height,
        zoom_cont_height = cont_height;
    if ($('.xbs_zoomdivider').length) {
        divider = $('.xbs_zoomdivider');
    }
    if ($('.xbs_zoomimageminus').length) {
        $('.xbs_zoomimageminus').click(function(){
            if(zoom_width > width/divider) {
                zoom_width = zoom_width/divider;
                zoom_height = zoom_height/divider;
                zoom_cont_height = zoom_cont_height/divider;
                $('.xbs_popup_container').css({'width': zoom_width+'px','height': zoom_cont_height+'px'});
                $('.xbs_zoomimage').css({'width': zoom_width+'px','height': zoom_height+'px'});
            }
        });
    }
    if ($('.xbs_zoomimageplus').length) {
        $('.xbs_zoomimageplus').click(function(){
            if(zoom_width < original_width) {
                zoom_width = zoom_width*divider;
                zoom_height = zoom_height*divider;
                zoom_cont_height = zoom_cont_height*divider;
                $('.xbs_popup_container').css({'width': zoom_width+'px','height': zoom_cont_height+'px'});
                $('.xbs_zoomimage').css({'width': zoom_width+'px','height': zoom_height+'px'});
            }
        });
    }
}
{/literal}
</script>
{undef $divider $image_width $image_height}