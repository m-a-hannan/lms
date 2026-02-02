const dropArea = document.getElementById("dropArea");
const fileInput = document.getElementById("fileInput");
const previewImage = document.getElementById("previewImage");

if (dropArea && fileInput) {
    // Open file picker on click
    dropArea.addEventListener("click", () => fileInput.click());
}

// Handle file selection
if (fileInput) {
    fileInput.addEventListener("change", () => {
        if (fileInput.files.length) {
            showPreview(fileInput.files[0]);
        }
    });
}

// Preview function
function showPreview(file) {
    if (!previewImage) {
        return;
    }
    const reader = new FileReader();
    reader.onload = () => {
        previewImage.src = reader.result;
        previewImage.classList.remove("d-none");
    };
    reader.readAsDataURL(file);
}

// Book cover change event
function previewNewCover(event) {
	const img = document.getElementById("previewImage");
	img.src = URL.createObjectURL(event.target.files[0]);
	img.classList.remove("d-none");
}

document.addEventListener("DOMContentLoaded", () => {
	const sidebarInput = document.getElementById("sidebarSearchInput");
	const sidebarSuggest = document.getElementById("sidebarSearchSuggest");
	const sidebarReset = document.getElementById("sidebarSearchReset");
	const sidebarNav = document.getElementById("sidebar-navigation")
		|| (sidebarInput ? sidebarInput.closest(".app-sidebar")?.querySelector(".sidebar-menu") : null);

	if (sidebarInput && sidebarNav) {
		sidebarInput.addEventListener("keydown", (event) => {
			if (event.key === "Enter") {
				event.preventDefault();
			}
			event.stopPropagation();
		});
		sidebarInput.addEventListener("keyup", (event) => {
			event.stopPropagation();
		});
		sidebarInput.addEventListener("click", (event) => {
			event.stopPropagation();
		});

		const navItems = Array.from(sidebarNav.querySelectorAll(".nav-item"));
		const links = Array.from(sidebarNav.querySelectorAll("a.nav-link")).filter((link) => {
			const href = link.getAttribute("href");
			return href && href !== "#";
		});

		const items = links.map((link) => {
			const labelNode = link.querySelector("p");
			const label = ((labelNode ? labelNode.textContent : link.textContent) || "").trim();
			return {
				link,
				label,
				labelLower: label.toLowerCase(),
				li: link.closest(".nav-item"),
			};
		});

		const clearSidebarSuggest = () => {
			if (sidebarSuggest) {
				sidebarSuggest.innerHTML = "";
				sidebarSuggest.classList.remove("show");
			}
		};

		const renderSidebarSuggest = (matches) => {
			if (!sidebarSuggest) {
				return;
			}
			if (!matches.length) {
				clearSidebarSuggest();
				return;
			}
			sidebarSuggest.innerHTML = matches
				.map((item) => {
					const href = item.link.getAttribute("href") || "#";
					return `
						<a href="${href}" class="sidebar-suggest-item">
							${item.label}
						</a>
					`;
				})
				.join("");
			sidebarSuggest.classList.add("show");
		};

		const showAllNav = () => {
			const hiddenItems = Array.from(sidebarNav.querySelectorAll(".sidebar-hidden"));
			hiddenItems.forEach((item) => {
				item.classList.remove("sidebar-hidden");
				item.style.display = "";
			});
			navItems.forEach((item) => {
				item.classList.remove("sidebar-hidden");
				item.style.display = "";
				item.classList.remove("menu-open");
				const link = item.querySelector(":scope > .nav-link");
				if (link) {
					link.classList.remove("active");
				}
			});
			const trees = Array.from(sidebarNav.querySelectorAll(".nav-treeview"));
			trees.forEach((tree) => {
				tree.style.removeProperty("display");
				tree.style.removeProperty("height");
			});
		};

		const resetSidebarSearch = (clearValue = false) => {
			showAllNav();
			clearSidebarSuggest();
			if (clearValue && sidebarInput) {
				sidebarInput.value = "";
			}
		};

		const filterSidebar = (term) => {
			const query = term.trim().toLowerCase();
			if (query.length < 2) {
				resetSidebarSearch(false);
				return;
			}

			const matches = items.filter((item) => item.labelLower.includes(query));
			const visible = new Set();
			matches.forEach((item) => {
				let current = item.li;
				while (current) {
					visible.add(current);
					if (!current.parentElement) {
						break;
					}
					current = current.parentElement.closest(".nav-item");
				}
			});

			navItems.forEach((item) => {
				if (visible.has(item)) {
					item.classList.remove("sidebar-hidden");
					item.style.display = "";
				} else {
					item.classList.add("sidebar-hidden");
					item.style.display = "none";
				}
			});

			navItems.forEach((item) => item.classList.remove("menu-open"));
			matches.forEach((item) => {
				if (!item.li || !item.li.parentElement) {
					return;
				}
				const parent = item.li.parentElement.closest(".nav-item");
				if (parent) {
					parent.classList.add("menu-open");
					const parentLink = parent.querySelector(":scope > .nav-link");
					if (parentLink) {
						parentLink.classList.add("active");
					}
					const tree = parent.querySelector(":scope > .nav-treeview");
					if (tree) {
						tree.style.display = "block";
					}
				}
			});

			renderSidebarSuggest(matches.slice(0, 6));
		};

		sidebarInput.addEventListener("input", (event) => filterSidebar(event.target.value));
		sidebarInput.addEventListener("input", (event) => {
			if (event.target.value.trim() === "") {
				resetSidebarSearch(true);
			}
		});

		if (sidebarReset) {
			sidebarReset.addEventListener("click", () => {
				resetSidebarSearch(true);
				sidebarInput.focus();
			});
		}

		document.addEventListener("click", (event) => {
			if (!sidebarInput) {
				return;
			}
			if (sidebarInput.contains(event.target)) {
				return;
			}
			if (sidebarSuggest && sidebarSuggest.contains(event.target)) {
				return;
			}
			const sidebarLink = event.target.closest(".app-sidebar a.nav-link");
			if (sidebarLink) {
				return;
			}
			resetSidebarSearch(true);
		});

		if (sidebarSuggest) {
			sidebarSuggest.addEventListener("mousedown", (event) => {
				const link = event.target.closest(".sidebar-suggest-item");
				if (!link) {
					return;
				}
				event.preventDefault();
				window.location.href = link.getAttribute("href");
			});
		}
	}

	const pageSearchInput = document.getElementById("pageSearchInput");
	if (pageSearchInput) {
		const topbarSearch = document.getElementById("topbarSearch");

		const expandTopbarSearch = () => {
			if (topbarSearch) {
				topbarSearch.classList.add("active");
			}
			pageSearchInput.focus();
		};

		const collapseTopbarSearch = () => {
			if (!topbarSearch) {
				return;
			}
			if (pageSearchInput.value.trim() !== "") {
				return;
			}
			topbarSearch.classList.remove("active");
		};

		if (topbarSearch) {
			topbarSearch.addEventListener("click", expandTopbarSearch);
		}
		pageSearchInput.addEventListener("focus", expandTopbarSearch);
		pageSearchInput.addEventListener("blur", collapseTopbarSearch);

		const filterRows = (term) => {
			const query = term.trim().toLowerCase();
			const tables = Array.from(document.querySelectorAll(".app-content table"));
			if (!tables.length) {
				return;
			}
			tables.forEach((table) => {
				const rows = Array.from(table.querySelectorAll("tbody tr"));
				rows.forEach((row) => {
					if (!query) {
						row.classList.remove("d-none");
						return;
					}
					const text = row.textContent.toLowerCase();
					if (text.includes(query)) {
						row.classList.remove("d-none");
					} else {
						row.classList.add("d-none");
					}
				});
			});
		};

		pageSearchInput.addEventListener("input", (event) => {
			filterRows(event.target.value);
		});
	}
});
