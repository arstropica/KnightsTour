var knight = $('#knight').get(0), $slider;
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
		alignKnight();
	}
	$(window).resize(windowResize);
	windowResize();

	function getHistory(step) {
		return data['history'][step] === undefined ? false
				: data['history'][step];
	}
	
	function index2Board(index) {
		var size = Math.sqrt(data.total);
		return [Math.ceil(index / size), ((index - 1) % size) + 1];
	}
	
	function getCoverage(step) {
		return data['scoverage'][step] === undefined ? false
				: data['scoverage'][step];
	}

	function getSquare(value) {
		var square = false, loc,
		pos = getHistory(value);
		if (pos) {
			loc = index2Board(pos);
			square = $('#board').find('#' + loc[0] + 'x' + loc[1]).get(0);
		}
		return square;
	}
	
	function getLocalEfficiency(step) {
		var current = $(".board_square.active").length;
		return step ? Math.round((current / (step + 1)) * 10000) / 100 : 100; 
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
	
	function alignKnight() {
		if ($slider === undefined) return;
		var value = $slider.slider('getValue');
		var square = getSquare(value);
		var $square = $('#' + square.id);
		if (square) {
			var rect = getLocation(square);
			moveKnight(rect);
		}
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
			$('#meff').text(getLocalEfficiency(newVal) + '%');
			$('#scov').text((Math.round((Object.keys(getCoverage(newVal)).length / 7) * 10000) / 100) + '%');
		}
	}

	$slider = $('#slider').slider(
			{
				formatter : function(value) {
					var indices = index2Board(getHistory(value));
					var loc = indices ? [ String.fromCharCode(96 + indices[1]),
							indices[0] ].join('') : 'None';
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