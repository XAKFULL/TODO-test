export class Tag {
    constructor(data) {
        if (typeof data === 'string') {
            this.id = null;
            this.name = data;
        } else {
            const { id = null, name = '' } = data || {};
            this.id = id;
            this.name = name;
        }
    }

    toDTO() {
        return {
            id: this.id,
            name: this.name
        };
    }
}