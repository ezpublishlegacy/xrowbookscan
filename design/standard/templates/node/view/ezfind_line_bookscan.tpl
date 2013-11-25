{def $random_id = rand( 0, 156546470 )}
<div class="xbs_container">
{if is_set( $node.data_map.image )}
    {if $node.data_map.image.has_content}
        <div class="xbs_showpreview" data-pid="{$node.node_id|xbs_secure()}" data-contid="{$random_id}">{attribute_view_gui attribute=$node.data_map.image image_class=small}</div>
    {/if}
{/if}
    <div class="xbs_highlight">{$node.highlight} <span class="xbs_showpreview xbs_more" data-pid="{$node.node_id|xbs_secure()}" data-contid="{$random_id}">mehr</span></div>
</div>