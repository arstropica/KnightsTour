var knight = $('#knight').get(0);
$(function() {
	function windowResize() {
		var $labels = $('.board_label').empty();
		var size = $(".board_square").width();
		var $squares = $(".board_square");
		$squares.width(size);
		$labels.width(size);
		$squares.height(size);
		$('#knight').width(size * .75);
		$('#knight').height(size * .75);
		$labels.each(function() {
			$(this).text($(this).data('value'));
		});
	}
	$(window).resize(windowResize);
	windowResize();

	function getIndices(step) {
		return data['history'][step] === undefined ? false
				: data['history'][step];
	}

	function getSquare(value) {
		var square = false;
		var loc = getIndices(value);
		if (loc) {
			square = $('#board').find('#' + loc[0] + 'x' + loc[1]).get(0);
		}
		return square;
	}

	function getLocation(element) {
		var vpos = document.body.getBoundingClientRect();
		var lpos = element.getBoundingClientRect();
		var hw = element.offsetWidth / 4;
		var hh = element.offsetHeight / 4;
		return {
			left : lpos.left - vpos.left + hw,
			top : lpos.top - vpos.top + hh
		};
	}

	function moveKnight(rect) {
		knight.style.left = rect.left - (knight.offsetWidth / 4) + 'px';
		knight.style.top = rect.top + 'px';
	}

	function onChange(oldVal, newVal) {
		var square = getSquare(newVal);
		var $square = $('#' + square.id);
		if (square) {
			if ($square.data('knight') === undefined) {
				$square.data('knight', newVal);
			}
			var rect = getLocation(square);
			moveKnight(rect);
			if (oldVal > newVal) {
				square = getSquare(oldVal);
				$square = $('#' + square.id);
				if ($square.data('knight') == oldVal) {
					$square.empty();
				}
				$square.removeClass('active');
			} else if ($square.is(':empty')) {
				$square.html('<span>' + (parseInt(newVal, 10) + 1) + '</span>');
				$square.addClass('active');
			} else {
				$square.addClass('active');
			}
		}
	}

	$('#slider').slider(
			{
				formatter : function(value) {
					var indices = getIndices(value);
					var loc = indices ? [ String.fromCharCode(97 + indices[1]),
							size - indices[0] ].join('') : 'None';
					return 'Current Location: ' + loc;
				}
			}).on('change', function(e) {
		var newVal, oldVal;
		if (e.value === undefined) {
			newVal = oldVal = 0;
		} else {
			newVal = parseInt(e.value.newValue.toString(), 10);
			oldVal = parseInt(e.value.oldValue.toString(), 10);
		}
		if (Math.abs(newVal - oldVal) > 1) {
			var diff = newVal - oldVal;
			if (diff < 0) {
				for (var i = 0; i > diff; i--) {
					onChange(oldVal + i, oldVal - 1 + i);
				}
			} else {
				for (var i = 0; i < diff; i++) {
					onChange(oldVal + i, oldVal + 1 + i);
				}
			}
		} else {
			onChange(oldVal, newVal);
		}
	});
	$('#slider').trigger('change');
});