class TagsManager {
    constructor(containerId, inputId) {
        this.container = document.getElementById(containerId);
        this.input = document.getElementById(inputId);
        this.tags = new Set();

        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.input.value.trim();
                if (value && !this.tags.has(value)) {
                    this.addTag(value);
                    this.input.value = '';
                }
            }
        });
    }

    addTag(tag) {
        this.tags.add(tag);
        this.render();
    }

    removeTag(tag) {
        this.tags.delete(tag);
        this.render();
    }

    getTags() {
        return Array.from(this.tags);
    }

    setTagsFromString(tagsString) {
        this.tags.clear();
        if (tagsString) {
            let arr;
            try {
                arr = JSON.parse(tagsString);
                if (!Array.isArray(arr)) arr = [];
            } catch {
                arr = tagsString.split(',').map(t => t.trim()).filter(t => t.length > 0);
            }
            arr.forEach(t => this.tags.add(t));
        }
        this.render();
    }

    render() {
        this.container.innerHTML = '';

        // Ordenar alfabéticamente
        const sortedTags = Array.from(this.tags).sort((a, b) => a.localeCompare(b));

        sortedTags.forEach(tag => {
            const chip = document.createElement('span');
            chip.className = 'tag-chip';

            // Clasificar por longitud
            if (tag.length <= 4) chip.classList.add('short');
            else if (tag.length <= 8) chip.classList.add('medium');
            else chip.classList.add('long');

            chip.textContent = tag;

            const removeBtn = document.createElement('span');
            removeBtn.className = 'remove-tag';
            removeBtn.textContent = '×';
            removeBtn.title = "Eliminar etiqueta";
            removeBtn.addEventListener('click', () => {
                this.removeTag(tag);
            });

            chip.appendChild(removeBtn);
            this.container.appendChild(chip);
        });
    }
}

let tagsManager;

document.addEventListener('DOMContentLoaded', () => {
    tagsManager = new TagsManager('tagsContainer', 'tagInput');
});
