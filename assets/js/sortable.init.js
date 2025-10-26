document.addEventListener('DOMContentLoaded', function () {
	const el = document.getElementById('menu-list');
	if (el) {
		new Sortable(el, {
			animation: 150,
			onEnd: function () {
				const rows = document.querySelectorAll('#menu-list tr');
				rows.forEach((row, index) => {
					const input = row.querySelector('input[type=\"number\"]');
					if (input) {
						input.value = index + 1;
					}
				});
			}
		});
	}
});