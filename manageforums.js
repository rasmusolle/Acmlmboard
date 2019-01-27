// This file must be saved as UTF-8 or the localmod name search will break

function toggleAll(cls, enable)
{
	var elems = document.getElementsByClassName(cls);
	for (var i = 0; i < elems.length; i++)
		elems[i].disabled = !enable;
}

function ajaxGet(url, callback)
{
	var x = new XMLHttpRequest();
	x.onreadystatechange = function() 
	{
        if (this.readyState == 4 && this.status == 200) 
		{
            callback(this.responseText);
        }
    };
	x.open('GET', url);
	x.send(null);
}


function localmodSearch(field)
{
	var reslist = document.getElementById('addmod_list');
	
	var srch = field.value;
	if (srch.length < 3) return;
	
	ajaxGet('JSUserSearchByName.php?a=' + encodeURIComponent(srch), 
	function(res)
	{
		while (reslist.length > 0)
			reslist.remove(0);
		
		var lines = res.split('\n');
		for (var l = 0; l < lines.length; l++)
		{
			var line = lines[l].trim();
			if (line.length < 1) continue;
			
			var sep = line.lastIndexOf('¬');
			var username = line.substring(0,sep);
			var userid = line.substring(sep+1);
			
			var opt = document.createElement('option');
			opt.value = userid;
			opt.text = username;
			reslist.add(opt, null);
		}
	});
}

function chooseLocalmod(field)
{
	var text = field.options[field.selectedIndex].text;
	document.getElementById('addmod_name').value = text;
}

function addLocalmod()
{
	var field = document.getElementById('addmod_name');
	var user = field.value;
	ajaxGet('manageforums.php?ajax=localmodRow&user=' + encodeURIComponent(user), 
	function(res)
	{
		if (!res)
		{
			alert('Error: user \''+user+'\' could not be found.');
			return;
		}
		
		res = res.split('|');
		if (document.getElementById('localmod_'+res[0]))
		{
			alert('Error: user \''+user+'\' is already assigned to this forum.');
			return;
		}
		
		var row = document.createElement('div');
		row.innerHTML = res[1];
		
		document.getElementById('modlist').appendChild(row);
	});
}

function deleteLocalmod(elem)
{
	elem.parentNode.removeChild(elem);
}
