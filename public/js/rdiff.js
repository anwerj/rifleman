var rdiff = function (options)
{
    //var preferred = options.preferred;

    var getPreferred = function (all)
    {
        return options.preferred || all[0];
    }

    var getCompiler = function (type)
    {
        return options.compilers[type];
    }

    var getTarget = function (items, type)
    {
        var targets = {};
        // #<id> .<type>_content
        for (var id in items)
        {
            targets[id] = $('#'+id +' .parsed_content').html('');
        }
        return targets;
    };

    return {
        handleFile : function (files)
        {
            var entries = JSON.parse(JSON.stringify(files));
            var index, ldColumn, liColumn, entry, pref, item, name;
            var all = Object.keys(entries);
            var pre = getPreferred(all);
            var targets = getTarget(entries, 'file');
            var ldCompiler = getCompiler('ld');
            var liCompiler = getCompiler('li');
            var items = {};
            var maxLines = 0;
            var entryHtml = '<pre class="line_info file_line"></pre><pre class="line_detail file_line prettyprint"></pre>'

            for(index in entries)
            {
                items = entries[index].content.body;
                maxLines = Math.max(maxLines, items.length);
                targets[index].html(entryHtml);
                ldColumn = targets[index].find('.line_detail');
                liColumn = targets[index].find('.line_info');
                for(item in items)
                {
                    ldColumn.append(ldCompiler({entry:items[item], entryIndex : index, itemIndex:item}));
                    liColumn.append(liCompiler({entry:items[item], entryIndex : index, itemIndex:item}));
                }
            }
        },

        handleList : function (list)
        {
            var entries = JSON.parse(JSON.stringify(list));
            var index, column, entry, pref, item, name;
            var all = Object.keys(entries);
            var pre = list[getPreferred(all)];
            var targets = getTarget(entries, 'list');
            var lCompiler = getCompiler('list');
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
                    targets[index].append(lCompiler({entry : entry, entryIndex : index, itemIndex : name}))
                }
            }

            return items;
        }
    };
}
