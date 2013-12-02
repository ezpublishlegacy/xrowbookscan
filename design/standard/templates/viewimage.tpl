{def $divider = ezini( 'Settings', 'ShowImageDivider', 'xrowbookscan.ini' )
     $image_width = $image.width|div( $divider )
     $image_height = $image.height|div( $divider )
     $width = $image_width
     $height = $image_height|sum( 40 )}
<div style="height:{$height}px; width: {$width}px; display: none;" id="xbs_popup_{$contid}" class="xbs_popup_container">
    <span class="xbs_popup_close">X</span>
    <div class="xbs_popcontent">
        <div class="xbs_zoomimageplus">Zoom ++</div><div class="xbs_zoomimageminus">Zoom --</div>
    </div>
    <img src={$image.url|ezurl()} style="width: {$image_width}px; height: {$image_height}px" class="xbs_zoomimage" />
</div>
<script type="text/javascript">
{literal}
if (jQuery('.xbs_zoomimage').length) {
    var divider = {/literal}{$divider}{literal},
        original_width = {/literal}{$image.width}{literal},
        width = {/literal}{$image_width}{literal},
        height = {/literal}{$image_height}{literal},
        cont_width = {/literal}{$width}{literal},
        cont_height = {/literal}{$height}{literal},
        zoom_width = width,
        zoom_height = height,
        zoom_cont_width = cont_width,
        zoom_cont_height = cont_height;
    if (jQuery('.xbs_zoomdivider').length) {
        divider = jQuery('.xbs_zoomdivider');
    }
    if (jQuery('.xbs_zoomimageminus').length) {
        jQuery('.xbs_zoomimageminus').click(function(){
            if(zoom_width > width/divider) {
                zoom_width = zoom_width/divider;
                zoom_height = zoom_height/divider;
                zoom_cont_width = zoom_cont_width/divider;
                zoom_cont_height = zoom_cont_height/divider;
                jQuery('.xbs_popup_container').css({'width': zoom_cont_width+'px','height': zoom_cont_height+'px'});
                jQuery('.xbs_zoomimage').css({'width': zoom_width+'px','height': zoom_height+'px'});
            }
        });
    }
    if (jQuery('.xbs_zoomimageplus').length) {
        jQuery('.xbs_zoomimageplus').click(function(){
            if(zoom_width < original_width) {
                zoom_width = zoom_width*divider;
                zoom_height = zoom_height*divider;
                zoom_cont_width = zoom_cont_width*divider;
                zoom_cont_height = zoom_cont_height*divider;
                jQuery('.xbs_popup_container').css({'width': zoom_cont_width+'px','height': zoom_cont_height+'px'});
                jQuery('.xbs_zoomimage').css({'width': zoom_width+'px','height': zoom_height+'px'});
            }
        });
    }
}
{/literal}
</script>
{undef $divider $image_width $image_height}