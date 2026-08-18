/**
 * Teznevise flexible page builder — admin repeater UI.
 *
 * State lives in a JavaScript array that mirrors the JSON payload stored in the
 * `_teznevise_builder_sections` post meta. Structural changes (add, duplicate,
 * remove, move, drag) re-render the list; field edits patch the state in place so
 * the focused input is never rebuilt.
 */
(function ($) {
	'use strict';

	var config = window.teznevise_builder_config || {};
	var types = config.types || {};
	var sectionFields = config.sectionFields || {};
	var iconChoices = config.iconChoices || {};
	var colorChoices = config.colorChoices || {};
	var i18n = config.i18n || {};

	function t(key, fallback) {
		return i18n[key] || fallback || key;
	}

	function esc(value) {
		return $('<div/>').text(value === undefined || value === null ? '' : String(value)).html();
	}

	function defaultValue(field) {
		if (field && typeof field.default !== 'undefined') {
			return field.default;
		}
		if (field && field.type === 'select' && field.choices) {
			return Object.keys(field.choices)[0] || '';
		}
		if (field && field.type === 'color') {
			return Object.keys(colorChoices)[0] || 'icon-teal';
		}
		return '';
	}

	function newItem(typeKey) {
		var definition = types[typeKey] || {};
		var fields = definition.itemFields || {};
		var item = {};
		Object.keys(fields).forEach(function (key) {
			item[key] = defaultValue(fields[key]);
			if (fields[key].type === 'image') {
				item[key + '_id'] = 0;
			}
		});
		return item;
	}

	function newSection(typeKey) {
		var definition = types[typeKey] || {};
		var section = { type: typeKey, enabled: true, items: [] };
		(definition.supports || []).forEach(function (key) {
			section[key] = defaultValue(sectionFields[key]);
		});
		if ((definition.itemFields || {}) && Object.keys(definition.itemFields || {}).length) {
			section.items.push(newItem(typeKey));
		}
		return section;
	}

	function clone(value) {
		return JSON.parse(JSON.stringify(value));
	}

	function Builder(root) {
		this.$root = $(root);
		this.$sections = this.$root.find('[data-tez-builder-sections]');
		this.$payload = this.$root.find('[data-tez-builder-payload]');
		this.$typeSelect = this.$root.find('[data-tez-builder-type]');
		this.state = this.readPayload();
		this.bind();
		this.render();
	}

	Builder.prototype.readPayload = function () {
		var raw = this.$payload.val();
		if (!raw) {
			return [];
		}
		try {
			var parsed = JSON.parse(raw);
			return Array.isArray(parsed) ? parsed : [];
		} catch (e) {
			return [];
		}
	};

	Builder.prototype.sync = function () {
		this.$payload.val(JSON.stringify(this.state));
	};

	Builder.prototype.bind = function () {
		var self = this;

		this.$root.on('click', '[data-tez-builder-add]', function (event) {
			event.preventDefault();
			var typeKey = self.$typeSelect.val();
			if (!types[typeKey]) {
				return;
			}
			self.state.push(newSection(typeKey));
			self.render();
		});

		this.$root.on('click', '[data-action]', function (event) {
			var $button = $(this);
			var action = $button.data('action');
			var sectionIndex = parseInt($button.closest('[data-section-index]').attr('data-section-index'), 10);
			var $item = $button.closest('[data-item-index]');
			var itemIndex = $item.length ? parseInt($item.attr('data-item-index'), 10) : -1;
			event.preventDefault();
			self.handleAction(action, sectionIndex, itemIndex);
		});

		this.$root.on('input change', '[data-field-key]', function () {
			var $input = $(this);
			var key = $input.attr('data-field-key');
			var target = self.resolveTarget($input);
			if (!target) {
				return;
			}
			if ($input.is(':checkbox')) {
				target[key] = $input.is(':checked');
			} else {
				target[key] = $input.val();
			}
			self.afterFieldChange($input, target, key);
			self.sync();
		});

		this.$root.on('change', '[data-icon-picker]', function () {
			var value = $(this).val();
			if (!value) {
				return;
			}
			$(this)
				.closest('.tez-builder-icon-control')
				.find('[data-field-key]')
				.val(value)
				.trigger('change');
		});

		this.$root.on('click', '[data-media-choose]', function (event) {
			event.preventDefault();
			self.openMedia($(this));
		});

		this.$root.on('click', '[data-media-clear]', function (event) {
			event.preventDefault();
			var $control = $(this).closest('.tez-builder-media');
			$control.find('[data-field-key]').val('').trigger('change');
			var target = self.resolveTarget($control.find('[data-field-key]'));
			var key = $control.find('[data-field-key]').attr('data-field-key');
			if (target) {
				target[key + '_id'] = 0;
			}
			$control.find('[data-media-preview]').empty();
			self.sync();
		});

		this.$root.closest('form').on('submit', function () {
			self.sync();
		});
	};

	Builder.prototype.resolveTarget = function ($input) {
		var sectionIndex = parseInt($input.closest('[data-section-index]').attr('data-section-index'), 10);
		if (isNaN(sectionIndex) || !this.state[sectionIndex]) {
			return null;
		}
		var $item = $input.closest('[data-item-index]');
		if (!$item.length) {
			return this.state[sectionIndex];
		}
		var itemIndex = parseInt($item.attr('data-item-index'), 10);
		return this.state[sectionIndex].items[itemIndex] || null;
	};

	Builder.prototype.afterFieldChange = function ($input, target, key) {
		if ($input.attr('data-field-type') === 'icon') {
			$input.closest('.tez-builder-icon-control').find('[data-icon-preview] i').attr('class', target[key] || 'fa-solid fa-circle');
		}
		if (key === 'title') {
			var $section = $input.closest('[data-section-index]');
			var sectionIndex = parseInt($section.attr('data-section-index'), 10);
			if (!$input.closest('[data-item-index]').length) {
				$section
					.find('> .tez-builder-section-head [data-section-summary]')
					.text(this.state[sectionIndex].title || t('untitled'));
			} else {
				$input.closest('[data-item-index]').find('[data-item-summary]').text(target[key] || t('untitled'));
			}
		}
	};

	Builder.prototype.handleAction = function (action, sectionIndex, itemIndex) {
		var section = this.state[sectionIndex];
		if (!section) {
			return;
		}

		switch (action) {
			case 'section-remove':
				if (!window.confirm(t('confirmSection'))) {
					return;
				}
				this.state.splice(sectionIndex, 1);
				break;
			case 'section-duplicate':
				this.state.splice(sectionIndex + 1, 0, clone(section));
				break;
			case 'section-up':
				if (sectionIndex === 0) {
					return;
				}
				this.state.splice(sectionIndex - 1, 0, this.state.splice(sectionIndex, 1)[0]);
				break;
			case 'section-down':
				if (sectionIndex >= this.state.length - 1) {
					return;
				}
				this.state.splice(sectionIndex + 1, 0, this.state.splice(sectionIndex, 1)[0]);
				break;
			case 'section-toggle-body':
				this.$sections
					.find('[data-section-index="' + sectionIndex + '"]')
					.toggleClass('is-collapsed');
				return;
			case 'item-add':
				section.items.push(newItem(section.type));
				break;
			case 'item-duplicate':
				if (itemIndex < 0 || !section.items[itemIndex]) {
					return;
				}
				section.items.splice(itemIndex + 1, 0, clone(section.items[itemIndex]));
				break;
			case 'item-remove':
				if (itemIndex < 0 || !window.confirm(t('confirmRemove'))) {
					return;
				}
				section.items.splice(itemIndex, 1);
				break;
			case 'item-up':
				if (itemIndex <= 0) {
					return;
				}
				section.items.splice(itemIndex - 1, 0, section.items.splice(itemIndex, 1)[0]);
				break;
			case 'item-down':
				if (itemIndex < 0 || itemIndex >= section.items.length - 1) {
					return;
				}
				section.items.splice(itemIndex + 1, 0, section.items.splice(itemIndex, 1)[0]);
				break;
			default:
				return;
		}

		this.render();
	};

	Builder.prototype.openMedia = function ($button) {
		var self = this;
		if (!window.wp || !window.wp.media) {
			return;
		}
		var $control = $button.closest('.tez-builder-media');
		var frame = window.wp.media({
			title: t('chooseImage'),
			button: { text: t('chooseImage') },
			multiple: false
		});
		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var $input = $control.find('[data-field-key]');
			var key = $input.attr('data-field-key');
			var target = self.resolveTarget($input);
			$input.val(attachment.url);
			$control
				.find('[data-media-preview]')
				.html('<img src="' + esc(attachment.url) + '" alt="" />');
			if (target) {
				target[key] = attachment.url;
				target[key + '_id'] = attachment.id;
			}
			self.sync();
		});
		frame.open();
	};

	Builder.prototype.renderField = function (key, field, value, extraClass) {
		var type = field.type || 'text';
		var attrs = 'data-field-key="' + esc(key) + '" data-field-type="' + esc(type) + '"';
		var html = '<div class="tez-builder-field ' + (extraClass || '') + ' tez-builder-field-' + esc(type) + '">';
		html += '<label>' + esc(field.label || key) + '</label>';

		if (type === 'textarea') {
			html += '<textarea rows="3" ' + attrs + '>' + esc(value) + '</textarea>';
		} else if (type === 'select' || type === 'color') {
			var choices = type === 'color' ? colorChoices : field.choices || {};
			html += '<select ' + attrs + '>';
			Object.keys(choices).forEach(function (choiceKey) {
				html +=
					'<option value="' + esc(choiceKey) + '"' +
					(String(value) === String(choiceKey) ? ' selected' : '') +
					'>' + esc(choices[choiceKey]) + '</option>';
			});
			html += '</select>';
		} else if (type === 'icon') {
			html += '<div class="tez-builder-icon-control">';
			html += '<select data-icon-picker>';
			Object.keys(iconChoices).forEach(function (choiceKey) {
				html +=
					'<option value="' + esc(choiceKey) + '"' +
					(String(value) === String(choiceKey) ? ' selected' : '') +
					'>' + esc(iconChoices[choiceKey]) + '</option>';
			});
			html += '</select>';
			html += '<input type="text" ' + attrs + ' value="' + esc(value) + '" placeholder="fa-solid fa-star" />';
			html += '<span class="tez-builder-icon-preview" data-icon-preview aria-hidden="true"><i class="' + esc(value || 'fa-solid fa-circle') + '"></i></span>';
			html += '</div>';
		} else if (type === 'image') {
			html += '<div class="tez-builder-media">';
			html += '<div class="tez-builder-media-preview" data-media-preview>';
			if (value) {
				html += '<img src="' + esc(value) + '" alt="" />';
			}
			html += '</div>';
			html += '<input type="text" ' + attrs + ' value="' + esc(value) + '" />';
			html += '<button type="button" class="button" data-media-choose>' + esc(t('chooseImage')) + '</button> ';
			html += '<button type="button" class="button-link" data-media-clear>' + esc(t('clearImage')) + '</button>';
			html += '</div>';
		} else {
			html += '<input type="text" ' + attrs + ' value="' + esc(value) + '" />';
		}

		if (field.description) {
			html += '<p class="description">' + esc(field.description) + '</p>';
		}
		return html + '</div>';
	};

	Builder.prototype.renderItem = function (section, item, itemIndex) {
		var self = this;
		var definition = types[section.type] || {};
		var fields = definition.itemFields || {};
		var html = '<div class="tez-builder-item" data-item-index="' + itemIndex + '">';
		html += '<div class="tez-builder-item-head">';
		html += '<span class="tez-builder-drag" title="' + esc(t('dragHint')) + '" aria-hidden="true">⋮⋮</span>';
		html += '<strong data-item-summary>' + esc(item.title || t('untitled')) + '</strong>';
		html += '<span class="tez-builder-item-actions">';
		html += '<button type="button" class="button-link" data-action="item-up">' + esc(t('moveUp')) + '</button>';
		html += '<button type="button" class="button-link" data-action="item-down">' + esc(t('moveDown')) + '</button>';
		html += '<button type="button" class="button-link" data-action="item-duplicate">' + esc(t('duplicate')) + '</button>';
		html += '<button type="button" class="button-link tez-builder-danger" data-action="item-remove">' + esc(t('remove')) + '</button>';
		html += '</span></div>';
		html += '<div class="tez-builder-item-body">';
		Object.keys(fields).forEach(function (key) {
			html += self.renderField(key, fields[key], item[key], 'tez-builder-item-field');
		});
		html += '</div></div>';
		return html;
	};

	Builder.prototype.renderSection = function (section, sectionIndex) {
		var self = this;
		var definition = types[section.type];
		if (!definition) {
			return '';
		}
		var html = '<div class="tez-builder-section" data-section-index="' + sectionIndex + '">';
		html += '<div class="tez-builder-section-head">';
		html += '<span class="tez-builder-drag tez-builder-section-drag" title="' + esc(t('dragHint')) + '" aria-hidden="true">⋮⋮</span>';
		html += '<button type="button" class="button-link tez-builder-collapse" data-action="section-toggle-body">▾</button>';
		html += '<span class="tez-builder-type-label">' + esc(definition.label) + '</span>';
		html += '<strong data-section-summary>' + esc(section.title || t('untitled')) + '</strong>';
		html += '<label class="tez-builder-enabled"><input type="checkbox" data-field-key="enabled"' + (section.enabled ? ' checked' : '') + ' /> ' + esc(t('enabled')) + '</label>';
		html += '<span class="tez-builder-section-actions">';
		html += '<button type="button" class="button-link" data-action="section-up">' + esc(t('moveUp')) + '</button>';
		html += '<button type="button" class="button-link" data-action="section-down">' + esc(t('moveDown')) + '</button>';
		html += '<button type="button" class="button-link" data-action="section-duplicate">' + esc(t('duplicate')) + '</button>';
		html += '<button type="button" class="button-link tez-builder-danger" data-action="section-remove">' + esc(t('remove')) + '</button>';
		html += '</span></div>';

		html += '<div class="tez-builder-section-body">';
		if (definition.description) {
			html += '<p class="description">' + esc(definition.description) + '</p>';
		}
		html += '<div class="tez-builder-section-fields">';
		(definition.supports || []).forEach(function (key) {
			if (!sectionFields[key]) {
				return;
			}
			html += self.renderField(key, sectionFields[key], section[key]);
		});
		html += '</div>';

		if (Object.keys(definition.itemFields || {}).length) {
			html += '<div class="tez-builder-items" data-tez-builder-items>';
			if (!section.items.length) {
				html += '<p class="description tez-builder-empty">' + esc(t('noItems')) + '</p>';
			}
			section.items.forEach(function (item, itemIndex) {
				html += self.renderItem(section, item, itemIndex);
			});
			html += '</div>';
			html += '<button type="button" class="button" data-action="item-add">+ ' + esc(definition.itemLabel || t('addItem')) + '</button>';
		}

		html += '</div></div>';
		return html;
	};

	Builder.prototype.render = function () {
		var self = this;
		var html = '';

		if (!this.state.length) {
			html = '<p class="description tez-builder-empty">' + esc(t('emptyState')) + '</p>';
		} else {
			this.state.forEach(function (section, index) {
				html += self.renderSection(section, index);
			});
		}

		this.$sections.html(html);
		this.initSortable();
		this.sync();
	};

	Builder.prototype.initSortable = function () {
		var self = this;
		if (!$.fn.sortable) {
			return;
		}

		this.$sections.sortable({
			handle: '.tez-builder-section-drag',
			items: '> .tez-builder-section',
			axis: 'y',
			start: function (event, ui) {
				ui.item.data('startIndex', ui.item.index());
			},
			update: function (event, ui) {
				var from = ui.item.data('startIndex');
				var to = ui.item.index();
				if (from === to) {
					return;
				}
				self.state.splice(to, 0, self.state.splice(from, 1)[0]);
				self.render();
			}
		});

		this.$sections.find('[data-tez-builder-items]').each(function () {
			var $items = $(this);
			var sectionIndex = parseInt($items.closest('[data-section-index]').attr('data-section-index'), 10);
			$items.sortable({
				handle: '.tez-builder-drag',
				items: '> .tez-builder-item',
				axis: 'y',
				start: function (event, ui) {
					ui.item.data('startIndex', ui.item.index());
				},
				update: function (event, ui) {
					var from = ui.item.data('startIndex');
					var to = ui.item.index();
					if (from === to) {
						return;
					}
					var items = self.state[sectionIndex].items;
					items.splice(to, 0, items.splice(from, 1)[0]);
					self.render();
				}
			});
		});
	};

	$(function () {
		$('[data-tez-builder]').each(function () {
			new Builder(this);
		});
	});
})(jQuery);
