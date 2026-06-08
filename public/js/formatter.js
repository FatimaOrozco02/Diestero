// Clase encargada de formatear capturas de información a lo largo del proyecto
class formatter {
    #textFormat;

    constructor() {}

    setFormat(text) {
        this.#textFormat = text;
        return this;
    }

    maxLength(length) {
        if (this.#textFormat.length > length) {
            this.#textFormat = this.#textFormat.substring(0, length);
        }
        return this;
    }

    noSpaces() {
        this.#textFormat = this.#textFormat.replace(/\s/g, "");
        return this;
    }

    noNumbers() {
        this.#textFormat = this.#textFormat.replace(/\d/g, "");
        return this;
    }

    noLetters() {
        this.#textFormat = this.#textFormat.replace(/[a-zA-Z]/g, "");
        return this;
    }

    noDashes() {
        this.#textFormat = this.#textFormat.replace(/[-_]/g, "");
        return this;
    }

    noDots() {
        this.#textFormat = this.#textFormat.replace(/\./g, "");
        return this;
    }

    getFormat() {
        return this.#textFormat;
    }
}