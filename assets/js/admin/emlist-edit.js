// console.log('heya');

jQuery(function() {

	$c = $('.meta-container');

	let input = (o = {}) => {
		if (!o.title) return;

		let c = document.createElement('div');
		c.classList.add('meta-div', o.title+'-input');

		let t = document.createElement('h4');
		t.appendChild(document.createTextNode(o.title);
		t.classList.add('meta-title', o.title+'-input');

		c.appendChild(t);

		let i = document.createElement('input');
		i.setAttribute('type', 'text');
		i.classList.add('meta-input', o.title+'-input');
		i.value = o.value || '';
		c.appendChild(i);

		return c;
	}

	console.log(JSON.stringify(data, null, 4));

	// console.log(data.meta);

	for (let d in data.meta)
		$c.append(input({title: d, value: data.meta[d]}));
		// $c.append($(input()).val(data.meta[d]));
		// console.log(data.meta[d]);

});