var knight = $('#knight').get(0);
$(function() {
	function windowResize() {
		var size = $(".board_square").width();
		$(".board_square").width(size);
		$(".board_square").height(size);
	}
	$(window).resize(windowResize);
	windowResize();

	function getSquare(value) {
		var square = false;
		var loc = data['history'][value] === undefined ? false
				: data['history'][value];
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
		knight.style.left = rect.left + 'px';
		knight.style.top = rect.top + 'px';
	}

	$('#slider').slider(
			{
				formatter : function(value) {
					var loc = data['history'][value] === undefined ? 'None'
							: data['history'][value].join(', ');
					return 'Current Location: ' + loc;
				}
			}).on('change', function(e) {
		var newVal, oldVal;
		if (e.value === undefined) {
			newVal = oldVal = 0;
		} else {
			newVal = e.value.newValue.toString();
			oldVal = e.value.oldValue.toString();
		}
		var square = getSquare(newVal);
		var rect = getLocation(square);
		moveKnight(rect);
		if (oldVal > newVal) {
			square = getSquare(oldVal);
			// $('#' + square.id).empty();
			$('#' + square.id).removeClass('active');
		} else if ($('#' + square.id).is(':empty')) {
			$('#' + square.id).html('<span>' + (parseInt(newVal, 10) + 1) + '</span>');
			$('#' + square.id).addClass('active');
		} else {
			$('#' + square.id).addClass('active');
		}

	});
	$('#slider').trigger('change');
});