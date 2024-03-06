window.select2Configs = window.select2Configs || {
    classSearching: ( input ) => {

        return {
            minimumInputLength: 0,
            placeholder: "Type class name to search classes",
            escapeMarkup: function (markup) { return markup; },
            ajax: {
                url: '/admin/search-classes',
                dataType: 'json',
                type: "GET",
                delay: 600,
                quietMillis: 50,
                data: function (term) {
                    return {
                        ...{term}, ...input
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return { id:item.id , text: item.name };
                        })
                    };
                }
            }
        }

    }
}