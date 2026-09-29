(function () {
	var directory = document.querySelector('.dynatafo-tag-directory');

	if (!directory) {
		return;
	}

	var input = directory.querySelector('#dynatafo-tag-filter');
	var rows = Array.prototype.slice.call(directory.querySelectorAll('.dynatafo-tag-row'));
	var chips = Array.prototype.slice.call(directory.querySelectorAll('.dynatafo-chip'));
	var licenseBtns = Array.prototype.slice.call(directory.querySelectorAll('.dynatafo-license-btn'));
	var emptyMessage = directory.querySelector('#dynatafo-tag-filter-empty');
	var activeCategory = 'all';
	var activeLicense = 'all';

	if (!input || !rows.length) {
		return;
	}

	var applyFilter = function () {
		var term = (input.value || '').toLowerCase().trim();
		var anyVisible = false;

		// Dynamically calculate tag counts per category based on active license
		var counts = { all: 0 };
		chips.forEach(function (chip) {
			var cat = chip.getAttribute('data-category');
			if (cat && cat !== 'all') {
				counts[cat] = 0;
			}
		});

		rows.forEach(function (row) {
			var rowCategory = (row.getAttribute('data-category') || '').toLowerCase();
			var rowIsPro = row.getAttribute('data-is-pro') === '1';
			var text = (row.textContent || '').toLowerCase();

			var licenseMatch = activeLicense === 'all' || 
							   (activeLicense === 'pro' && rowIsPro) || 
							   (activeLicense === 'free' && !rowIsPro);

			// Increment counts if the tag matches the active license filter
			if (licenseMatch) {
				counts.all++;
				if (rowCategory in counts) {
					counts[rowCategory]++;
				}
			}

			var categoryMatch = activeCategory === 'all' || rowCategory === activeCategory;
			var searchMatch = term === '' || text.indexOf(term) !== -1;
			var show = categoryMatch && searchMatch && licenseMatch;

			row.style.display = show ? '' : 'none';

			if (show) {
				anyVisible = true;
			}
		});

		// Update counts and visibility for all category chips
		chips.forEach(function (chip) {
			var cat = chip.getAttribute('data-category');
			var countSpan = chip.querySelector('.dynatafo-chip-count');
			if (countSpan && cat in counts) {
				countSpan.textContent = counts[cat];
			}

			// Hide category chips with 0 matching tags under the current license filter
			if (cat !== 'all') {
				var showChip = counts[cat] > 0;
				chip.style.display = showChip ? '' : 'none';
			}
		});

		// If the currently active category chip is hidden under this license filter,
		// reset active category to 'all' and apply the filter again.
		var activeChip = directory.querySelector('.dynatafo-chip.is-active');
		if (activeChip && activeChip.style.display === 'none' && activeCategory !== 'all') {
			activeCategory = 'all';
			chips.forEach(function (chip) {
				chip.classList.toggle('is-active', chip.getAttribute('data-category') === 'all');
			});
			applyFilter();
			return;
		}

		if (emptyMessage) {
			emptyMessage.style.display = anyVisible ? 'none' : '';
		}
	};

	chips.forEach(function (chip) {
		chip.addEventListener('click', function () {
			activeCategory = (chip.getAttribute('data-category') || 'all').toLowerCase();

			chips.forEach(function (otherChip) {
				otherChip.classList.toggle('is-active', otherChip === chip);
			});

			applyFilter();
		});
	});

	licenseBtns.forEach(function (btn) {
		btn.addEventListener('click', function () {
			activeLicense = (btn.getAttribute('data-license') || 'all').toLowerCase();

			licenseBtns.forEach(function (otherBtn) {
				otherBtn.classList.toggle('is-active', otherBtn === btn);
			});

			applyFilter();
		});
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === '/' && document.activeElement !== input) {
			event.preventDefault();
			input.focus();
		}
	});

	input.addEventListener('input', applyFilter);
	applyFilter();
})();
