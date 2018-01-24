var core = {
    // Used to hold compiled templates
    templates : {},

    // Used to hold custom handles
    handles : {},

    // Submits a form with ajax and called handler on that
    submit : function (event, target, handler)
    {
        event.preventDefault();
        var form   = $(target);
        var url    = form.attr('action');
        var method = form.attr('method');
        console.log(url, method);
        $.ajax({
            url     : url,
            method  : method,
            data    : form.serialize()
        }).done(function (response)
        {
            console.log('success', response);
            return core.response(handler, response);
        }).fail(function (xhr)
        {
            console.log('failure', xhr);
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
        if ('boolean' === typeof data.success)
        {
            data = [data];
        }

        var dataHandler;
        for(var i in data)
        {
            dataHandler = handler ? handler : (data.handler ? data.handler : 'handler');
            core.handle(handler, data[i], i);
        }
    },

    // Handles the data for ID
    handle : function (handler, data, id)
    {
        var compiler = core.ejs(handler);

        if(core.handles[handler])
        {
            core.handles[handler](compiler(data), data, id);
        }
        // Check for Default handler
        // Else throw error
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
