{***    please add this block outside the foreach search_result in search.tpl and expand the node_view like this:
        "{node_view_gui view=ezfind_line content_node=$result bookscan=$bookscan search_text=$search_text}"
         START    ***}
{*def $bookscan = hash( 'bookscanParentClass', ezini( 'Settings', 'ClassNameParentForBookscan', 'xrowbookscan.ini' ),
                        'bookscanClass', ezini( 'Settings', 'ClassIDForPreview', 'xrowbookscan.ini' ),
                        'limitBookscan', ezini( 'Settings', 'NumberOfPreviewPages', 'xrowbookscan.ini' ),
                        'bookscanSection', ezini( 'Settings', 'CreateBookscanSection', 'xrowbookscan.ini' ) )*}
{***    please add this block outside the foreach search_result END    ***}

<div class="content-view-line">
    <div class="class-article float-break">

        <div class="attribute-title"><a href={$node.url_alias|ezurl()}>{$node.name|wash}</a></div>

        {if is_set( $node.data_map.image )}
            {if $node.data_map.image.has_content}
                <div class="attribute-image">
                    {attribute_view_gui attribute=$node.data_map.image href=$node.url_alias|ezurl() image_class=small}
                </div>
            {/if}
        {/if}

        <div class="attribute-short">
            {$node.highlight} ({$node.score_percent|wash}%)
        </div>
    </div>
{if is_set( $bookscanParentClass )}
    {if $bookscanParentClass|contains( $node.class_identifier )}
    <div class="attribute-short" style="margin-left: 20px">
        {def $search_bookscan = fetch( ezfind, search, hash( 'query', $search_string,
                                                             'sort_by', hash( 'score', 'desc' ),
                                                             'class_id', array( $bookscan.bookscanClass ),
                                                             'limit', $bookscan.limitBookscan,
                                                             'subtree_array', array( $node.node_id ),
                                                             'limitation', hash( 'accessWord', 'limited', 
                                                                                 'policies', array( hash( 'Class', $bookscan.bookscanClass,
                                                                                                          'Section', array( $bookscan.bookscanSection ) ) ) ) ) )
             $search_result_bookscan = $search_bookscan['SearchResult']
             $search_count_bookscan = $search_bookscan['SearchCount']
             $search_extras_bookscan = $search_bookscan['SearchExtras']
             $stop_word_array_bookscan = $search_bookscan['StopWordArray']
             $search_data_bookscan = $search_bookscan}
        {if $search_count_bookscan|le( $bookscan.limitBookscan )}
        <div>Ihre Suche nach <strong>"{$search_string}"</strong> ergab <strong>{$search_count_bookscan}</strong> in "{$node.name|wash()}".</div>
        {else}
        <div>Ihre Suche nach <strong>"{$search_string}"</strong> ergab <strong>{$search_count_bookscan}</strong> in "{$node.name|wash()}". Die {$bookscan.limitBookscan} relevantesten Seiten werden Ihnen angezeigt.
        {/if}
        {if $search_count_bookscan|gt( 0 )}
            {foreach $search_bookscan as $bookscanItem}
                {node_view_gui view=ezfind_line_bookscan content_node=$bookscanItem}
            {/foreach}
        {/if}
    </div>
    {/if}
{else}
    <div class="message-error">WARNING: ClassNameParentForBookscan not set in xrowbookscan.ini or not added in search-template</div>
{/if}
</div>