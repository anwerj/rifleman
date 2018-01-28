var core =
{
    // Init methods, called once app is loaded
    onInit : {},

    // Direct function calls
    fn : {},

    // Used to hold compiled templates
    templates : {},

    // Used to hold custom handles
    handles : {},

    // Handle to be called on-ajax calls
    onAjax : {},

    // Custom functions written
    onCustom : {},

    // Called by app it self after load completes
    init : function ()
    {
        for (var i in core.onInit)
        {
            core.onInit[i](core);
        };
    },

    // Custom function must have handles
    // It can gracefully handle unloaded content
    custom : function (event, target, handler)
    {
        if(core.onCustom[handler])
        {
            return core.onCustom[handler](event, target);
        }
        console.log('No custom function found for '+handler);
    },

    // Submits a form with ajax and called handler on that
    submit : function (event, target, handler)
    {
        event.preventDefault();
        var form   = $(target);
        var url    = form.attr('action');
        var method = form.attr('method');

        this.handleOnAjax(event, handler, form);

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
    // Handles can we directly called only after page is loaded
    handle : function (handler, data)
    {
        if(core.handles[handler])
        {
            return core.handles[handler](data);
        }
        // Check for Default handler
        // Else throw error
        console.log('No handle found for '+handler);
    },

    // Handle to be called on ajax
    handleOnAjax : function (event, handler, target)
    {
        if(core.onAjax[handler])
        {
            core.onAjax[handler](event, target);
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
