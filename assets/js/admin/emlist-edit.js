// console.log('heya');

$(function() {

	console.log(JSON.stringify(data, null, 4));

	if (!data.meta) data.meta = {};

	let $c = $('.meta-container');

	let container = (o = {}) => {
		// if (!o.title) return;
		// console.log(o);
		let c = document.createElement('div');
		// console.log(o.title);
		c.classList.add('meta-div', o.name+'-div');

		let t = document.createElement('h4');
		t.appendChild(document.createTextNode(o.title.replace('-', ' ')));
		t.classList.add('meta-title', o.name+'-title');

		c.appendChild(t);
		return c;
	}

	let input = (o = {}) => {
		let c = container(o);

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

	let dropdown = (o = {}) => {
		let c = container(o);
		let s = document.createElement('select');
		s.setAttribute('name', data.name+'_data['+o.name+']');

		switch (o.value) {
			case 'en': o.value = 1; break;
			case 'to': o.value = 2; break;
			case 'tre': o.value = 3; break;
			case 'fire': o.value = 4; break;
			case 'fem': o.value = 5; break;
			case 'seks': o.value = 6; break;
		}

		let op = (i, t) => {
			let opt = document.createElement('option');
			opt.setAttribute('value', i);
			opt.appendChild(document.createTextNode(t));
			if (o.value == i) opt.setAttribute('selected', '');

			return opt;
		}

		s.appendChild(op('ingen', ''));
		if (o.min && o.max)
			for (let i = o.min; i <= o.max; i++)
				s.appendChild(op(i, i));

		c.appendChild(s);
		return c;
	}



	let image = (o = {}) => {
		if (!o.name) return '';

		// console.log('image', o);
		let image_container = document.createElement('div');
		image_container.classList.add('meta-div', 'image-div');
		// image_container.style.marginTop = '30px';
		// image_container.style.border = 'dotted 1px #eee';
		// image_container.style.marginRight = '20px';

		let image_input = document.createElement('input');
		image_input.setAttribute('hidden', '');
		image_input.setAttribute('name', data.name+'_data['+o.name+']');

		let image = document.createElement('img');
		image.style.display = 'block';
		
		// if (data.meta[o.name]) {
		image.setAttribute('src', data.meta[o.name]);	
		image_input.setAttribute('value', data.meta[o.name]);
		// }

		let button = document.createElement('button');
		button.setAttribute('type', 'button');
		button.appendChild(document.createTextNode(o.text || 'Velg bilde'));

		image_container.appendChild(button);
		image_container.appendChild(image_input);
		image_container.appendChild(image);

	 	button.addEventListener('click', (e) => {
	        e.preventDefault();

	        let custom_uploader = wp.media({
	            title: 'Custom Image',
	            button: {
	                text: 'Velg bilde'
	            },
	            multiple: false  // Set this to true to allow multiple files to be selected
	        }).on('select', function() {

	            let attachment = custom_uploader.state().get('selection').first().toJSON();
	            image.setAttribute('src', attachment.url);
	            image_input.setAttribute('value', attachment.url);

	        }).open();
	   	});
	   	return image_container;
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
	for (let d in data.template.meta) {
		if (data.template.meta[d].dropdown)
			meta.append(dropdown({
				title: data.template.meta[d].title,
				name: d,
				value: data.meta[d] || '',
				min: 1,
				max: 6
			}));

		else if (data.template.meta[d].image)
			meta.append(image({
				title: 'image',
				name: d,
				value: data.meta[d] || ''
			}));

		else
			meta.append(input({
				title: data.template.meta[d].title,
				name: d,
				value: data.meta[d] || ''
			}));
	}


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
		let val = fix($('#new'+data.name+'type').val().trim());
		sort.appendChild(input({
			title: val,
			name: data.name+'_sort_'+val,
			sort: true,
			value: data[data.name+'_sort_'+val] || '0'
		}));
	});


	$('#'+data.name+'typechecklist').on('change', function(e) {
		let text = fix($(e.target).parent().text().trim());
		console.log(data[data.name+'_sort_'+text]);
		if (!e.target.checked) $("input[name='"+data.name+"_sort_"+text+"']").parent().remove();
		else {
			sort.appendChild(input({
				title: text,
				name: data.name+'_sort_'+text,
				sort: true,
				value: data[data.name+'_sort_'+text] || '0'
			}));
		}
	});

});