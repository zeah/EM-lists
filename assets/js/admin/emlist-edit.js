// console.log('heya');

$(function() {

	console.log(JSON.stringify(data, null, 4));

	if (!data.meta) data.meta = {};

	let $c = $('.meta-container');

	let input = (o = {}) => {
		if (!o.title) return;

		let c = document.createElement('div');
		// console.log(o.title);
		c.classList.add('meta-div', o.name+'-div');

		let t = document.createElement('h4');
		t.appendChild(document.createTextNode(o.title.replace('-', ' ')));
		t.classList.add('meta-title', o.name+'-title');

		c.appendChild(t);

		let i = document.createElement('input');
		i.classList.add('meta-input', o.name+'-input');
		i.setAttribute('type', 'text');

		let cl = data.name+'_data['+o.name+']';
		if (o.sort) cl = o.name;

		i.setAttribute('name', cl);

		i.value = o.value || '';
		c.appendChild(i);

		return c;
	}

	let div = (o = {}) => {
		let c = document.createElement(o.element || 'div');
		c.classList.add(...o.class);

		return c;
	}

	let fix = e => {
		e = e.replace('æ', 'ae').replace('ø', 'o').replace('å', 'a').replace(' ', '-');

		return e;
	}

	let nav = div({'class': ['meta-nav']});

	let button = document.createElement('button');
	button.setAttribute('type', 'button');

	button.appendChild(document.createTextNode('Show structured data'));

	nav.appendChild(button);

	$c.append(nav);

	let meta = div({'class': ['meta-inner']});
	let struc = div({'class': ['struc-inner']});
	$(struc).hide();

	let sort = div({'class': ['meta-sort']});

	meta.append(sort);

	// adding sort 
	sort.appendChild(input({
		title: 'main',
		name: data.name+'_sort',
		sort: true,
		value: data[data.name+'_sort'] || 0
	}));
	for (let t in data.tax)
		sort.appendChild(input({
			title: data.tax[t],
			name: data.name+'_sort_'+data.tax[t],
			sort: true,
			value: data[data.name+'_sort_'+data.tax[t]] || 0
		}));


	// adding meta 
	for (let d in data.template.meta)
		meta.append(input({
			title: data.template.meta[d].title,
			name: d,
			value: data.meta[d] || ''
		}));


	// adding structured data
	for (let d in data.template.struc)
		struc.append(input({
			title: data.template.struc[d].title,
			name: d,
			value: data.meta[d] || ''
		}));


	$c.append(meta);
	$c.append(struc);

	$(button).click(() => {
		$([meta, struc]).toggle(300);
		$(button).text((i, text) => text === 'Show structured data' ? 'Show meta data' : 'Show structured data');
	});

	$('#'+data.name+'type-add-submit').click(function(e) {
		let val = $('#new'+data.name+'type').val().trim();
		sort.appendChild(input({
			title: fix(val),
			name: data.name+'_sort_'+fix(val),
			sort: true,
			value: data[data.name+'_sort_'+fix(val)] || 0
		}));
	});


	$('#'+data.name+'typechecklist').on('change', function(e) {
		let text = fix($(e.target).parent().text().trim());
		console.log(text);
		if (!e.target.checked) $("input[name='"+data.name+"_sort_"+text+"']").parent().remove();
		else {
			sort.appendChild(input({
				title: text,
				name: data.name+'_sort_'+text,
				sort: true,
				value: data[data.name+'_sort_'+text] || 0
			}));
		}
	});

});