var rdiff = function (options)
{
    //var preferred = options.preferred;

    var getPreferred = function (all)
    {
        return options.preferred || all[0];
    }

    var getCompiler = function ()
    {
        return options.compiler;
    }

    var getTarget = function (items, type)
    {
        var targets = {};
        // #<id> .<type>_content
        for (var id in items)
        {
            targets[id] = $('#'+id +' .'+ type+ '_content').html('');
        }
        return targets;
    };

    var getPositionInAll = function (entries, all, index)
    {
        var position = {};

        for(var id in all)
        {

        }
    }
    return {
        handleList : function (list)
        {
            var entries = JSON.parse(JSON.stringify(list));
            var index, column, entry, pref, item, name;
            var all = Object.keys(entries);
            var pre = list[getPreferred(all)];
            var targets = getTarget(entries, 'list');
            console.log(targets);
            var comiler = getCompiler();
            var items = {};

            // m*n ~ n^2
            for (index in entries)
            {
                for(name in entries[index].content)
                {
                    items[name] = {};
                }
            }

            for(name in items)
            {
                for (index in entries)
                {
                    entry = entries[index].content[name];
                    if ('undefined' === typeof entry)
                    {
                        entry = {name : name, _missing:true};
                    }
                    // items[entry][index] = item;
                    targets[index].append(comiler({entry : entry, entryIndex : index, itemIndex : name}))
                }
            }

            return items;
        }
    };
}
