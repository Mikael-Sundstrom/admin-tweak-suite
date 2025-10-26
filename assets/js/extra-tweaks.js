document.addEventListener('DOMContentLoaded', () => {
	// --- Extra Image Sizes: enable/disable width/height ---
	;['small', 'medium', 'large'].forEach((size) => {
		const checkbox = document.getElementById(`atweaks_enable_${size}`)
		const widthInput = document.querySelector(`input[name="atweaks_image_size_${size}_width"]`)
		const heightInput = document.querySelector(
			`input[name="atweaks_image_size_${size}_height"]`,
		)
		const cropInput = document.getElementById(`atweaks_crop_${size}`)
		if (!checkbox || !widthInput || !heightInput || !cropInput) return

		const syncDisabled = () => {
			const enabled = checkbox.checked
			widthInput.disabled = !enabled
			heightInput.disabled = !enabled
			if (cropInput) cropInput.disabled = !enabled // <-- lägg till detta
		}

		// Init + on change
		syncDisabled()
		checkbox.addEventListener('change', syncDisabled)

		// Sanera värden (1–4096)
		;[widthInput, heightInput].forEach((input) => {
			input.addEventListener('input', () => {
				let v = parseInt(input.value, 10)
				if (Number.isNaN(v) || v < 1) v = 1
				if (v > 4096) v = 4096
				input.value = v
			})
		})
	})

	// --- Shared Notes: enable/disable alla role-rutor ---
	const enableNotes = document.getElementById('atweaks_enable_shared_notes')
	const permissionBoxes = document.querySelectorAll('.shared-notes-permission')
	if (enableNotes && permissionBoxes.length) {
		const togglePermissions = () => {
			permissionBoxes.forEach((cb) => {
				cb.disabled = !enableNotes.checked
			})
		}
		togglePermissions()
		enableNotes.addEventListener('change', togglePermissions)
	}

	// --- Read/Write-koppling per roll (write kräver read) ---
	// matchar namn som atweaks_notes_read_{role}, atweaks_notes_write_{role}
	document.querySelectorAll('input[name^="atweaks_notes_write_"]').forEach((writeCb) => {
		const role = writeCb.name.replace('atweaks_notes_write_', '')
		const readCb = document.querySelector(`input[name="atweaks_notes_read_${role}"]`)
		if (!readCb) return

		writeCb.addEventListener('change', () => {
			if (writeCb.checked) readCb.checked = true
		})
		readCb.addEventListener('change', () => {
			if (!readCb.checked) writeCb.checked = false
		})
	})
})
