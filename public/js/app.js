var core = {
    // Used to hold compiled templates
    templates : {},

    // Used to hold custom handles
    handles : {},

    // Handle to be called on-ajax calls
    onAjax : {},

    // Submits a form with ajax and called handler on that
    submit : function (event, target, handler)
    {
        event.preventDefault();
        var form   = $(target);
        var url    = form.attr('action');
        var method = form.attr('method');

        this.handleOnAjax(handler);

        $.ajax({
            url     : url,
            method  : method,
            data    : form.serialize()
        }).done(function (response)
        {
            return core.response(handler, response);
        }).fail(function (xhr)
        {
            return core.response(handler, null, xhr);
        });

        return false;
    },

    // Handles response for given handler
    response : function (handler, data, failure)
    {
        if (data === null)
        {
            //handleFailure();
            return;
        }
        this.handle(handler, data)
    },

    // Handles the data for ID
    handle : function (handler, data)
    {
        if(core.handles[handler])
        {
            core.handles[handler](data);
        }

        // Check for Default handler
        // Else throw error
    },

    // Handle to be called on ajax
    handleOnAjax : function (handler)
    {
        if(core.onAjax[handler])
        {
            core.onAjax[handler]();
        }
    },

    // Returns compiled template for id
    ejs : function (id)
    {
        if (this.templates[id] === undefined)
        {
            var ejsOptions = {open :'{%', close: '%}'};
            this.templates[id] = ejs.compile($('#template_'+id).html(), ejsOptions);
        }
        return this.templates[id];
    }
}
