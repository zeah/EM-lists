// console.log('heya');

$(function() {

	if (!data.struc) data.struc = {};
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

	for (let d in data) 
		if (/^.+_sort.*$/.exec(d)) 
			sort.appendChild(input({
				title: fix(d.replace('_sort', '')).replace('-', ' '),
				name: d,
				value: data[d] || 0,
				sort: true
			}));

	if ($(sort).children().length == 0) 	
		for (let d in data.template.sort)
			sort.appendChild(input({
				title: data.template.sort[d].replace('_sort', ''),
				name: data.template.sort[d],
				value: 0,
				sort: true
			}));

	for (let d in data.template.meta)
		meta.append(input({
			title: data.template.meta[d].title,
			name: d,
			value: data.meta[d] || ''
		}));


	for (let d in data.template.struc)
		struc.append(input({
			title: data.template.struc[d].title,
			name: d,
			value: data.struc[d] || ''
		}));


	$c.append(meta);
	$c.append(struc);

	$(button).click(() => {
		$([meta, struc]).toggle(300);
		// $(struc).toggle(300);

		$(button).text((i, text) => text === 'Show structured data' ? 'Show meta data' : 'Show structured data');

		// console.log(this);
		// console.log($(this).text());
		// $(this).text('hji');

		// $(this).text(function(i, text) {
	        // return text === "PUSH ME" ? "DON'T PUSH ME" : "PUSH ME";
	    // });	
	});

	$('#'+data.name+'type-add-submit').click(function(e) {
		let val = $('#new'+data.name+'type').val().trim();
		sort.appendChild(input({
			title: fix(val),
			name: data.name+'_sort_'+fix(val),
			value: data[name+'_sort_'+fix(val)] || 0
		}));
	});


	$('#'+data.name+'typechecklist').on('change', function(e) {
		let text = fix($(e.target).parent().text().trim());
		if (!e.target.checked) $("input[name='kredittkort_sort_"+text+"']").parent().remove();
		else {
			sort.appendChild(input({
				title: text,
				name: data.name+'_sort_'+text,
				value: data[name+'_sort_'+text] || 0
			}));
		}
	});

});