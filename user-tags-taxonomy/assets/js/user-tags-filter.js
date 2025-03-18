jQuery(document).ready(function($) {
    $('#user_tag_filter').select2({
        ajax: {
            url: userTagsAjax.ajax_url,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    action: 'get_user_tags',
                    nonce: userTagsAjax.nonce,
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        },
        minimumInputLength: 1,
        placeholder: "Filter by User Tag",
        allowClear: true
    });
});
