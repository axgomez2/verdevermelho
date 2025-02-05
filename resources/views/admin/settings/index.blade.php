@extends('layouts.admin')

@section('content')
<div x-data="settingsManager()" class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Configurações</h1>

    <div x-data="{ activeTab: 'weights' }">
        <!-- Tab Navigation -->
        <div class="tabs tabs-boxed mb-4">
            <a class="tab" :class="{ 'tab-active': activeTab === 'weights' }" @click.prevent="activeTab = 'weights'">Pesos</a>
            <a class="tab" :class="{ 'tab-active': activeTab === 'dimensions' }" @click.prevent="activeTab = 'dimensions'">Dimensões</a>
            <a class="tab" :class="{ 'tab-active': activeTab === 'brands' }" @click.prevent="activeTab = 'brands'">Marcas</a>
            <a class="tab" :class="{ 'tab-active': activeTab === 'equipment-categories' }" @click.prevent="activeTab = 'equipment-categories'">Categorias de Equipamentos</a>
        </div>

        <!-- Tab Content -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <!-- Weights Tab -->
                <div x-show="activeTab === 'weights'">
                    <h2 class="card-title mb-4">Pesos</h2>
                    <form @submit.prevent="addWeight" class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-control">
                                <label for="weight_name" class="label">
                                    <span class="label-text">Nome</span>
                                </label>
                                <input type="text" x-model="newWeight.name" id="weight_name" required class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label for="weight_value" class="label">
                                    <span class="label-text">Valor</span>
                                </label>
                                <input type="number" step="0.01" x-model="newWeight.value" id="weight_value" required class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label for="weight_unit" class="label">
                                    <span class="label-text">Unidade</span>
                                </label>
                                <input type="text" x-model="newWeight.unit" id="weight_unit" required class="input input-bordered">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                Adicionar Peso
                            </button>
                        </div>
                    </form>
                    <ul class="menu bg-base-200 w-full rounded-box">
                        <template x-for="weight in weights" :key="weight.id">
                            <li>
                                <div class="flex items-center justify-between w-full">
                                    <template x-if="!weight.editing">
                                        <span x-text="`${weight.name} (${weight.value} ${weight.unit})`"></span>
                                    </template>
                                    <template x-if="weight.editing">
                                        <div class="flex items-center space-x-2">
                                            <input type="text" x-model="weight.name" class="input input-bordered input-sm">
                                            <input type="number" step="0.01" x-model="weight.value" class="input input-bordered input-sm">
                                            <input type="text" x-model="weight.unit" class="input input-bordered input-sm">
                                        </div>
                                    </template>
                                    <div>
                                        <button @click="toggleWeightEdit(weight)" class="btn btn-sm" x-text="weight.editing ? 'Salvar' : 'Editar'"></button>
                                        <button @click="deleteWeight(weight)" class="btn btn-sm btn-error">Excluir</button>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>

                <!-- Dimensions Tab -->
                <div x-show="activeTab === 'dimensions'">
                    <h2 class="card-title mb-4">Dimensões</h2>
                    <form @submit.prevent="addDimension" class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="form-control">
                                <label for="dimension_name" class="label">
                                    <span class="label-text">Nome</span>
                                </label>
                                <input type="text" x-model="newDimension.name" id="dimension_name" required class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label for="dimension_height" class="label">
                                    <span class="label-text">Altura</span>
                                </label>
                                <input type="number" step="0.01" x-model="newDimension.height" id="dimension_height" required class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label for="dimension_width" class="label">
                                    <span class="label-text">Largura</span>
                                </label>
                                <input type="number" step="0.01" x-model="newDimension.width" id="dimension_width" required class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label for="dimension_depth" class="label">
                                    <span class="label-text">Profundidade</span>
                                </label>
                                <input type="number" step="0.01" x-model="newDimension.depth" id="dimension_depth" required class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label for="dimension_unit" class="label">
                                    <span class="label-text">Unidade</span>
                                </label>
                                <input type="text" x-model="newDimension.unit" id="dimension_unit" required class="input input-bordered">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                Adicionar Dimensão
                            </button>
                        </div>
                    </form>
                    <ul class="menu bg-base-200 w-full rounded-box">
                        <template x-for="dimension in dimensions" :key="dimension.id">
                            <li>
                                <div class="flex items-center justify-between w-full">
                                    <template x-if="!dimension.editing">
                                        <span x-text="`${dimension.name} (${dimension.height}x${dimension.width}x${dimension.depth} ${dimension.unit})`"></span>
                                    </template>
                                    <template x-if="dimension.editing">
                                        <div class="flex items-center space-x-2">
                                            <input type="text" x-model="dimension.name" class="input input-bordered input-sm">
                                            <input type="number" step="0.01" x-model="dimension.height" class="input input-bordered input-sm">
                                            <input type="number" step="0.01" x-model="dimension.width" class="input input-bordered input-sm">
                                            <input type="number" step="0.01" x-model="dimension.depth" class="input input-bordered input-sm">
                                            <input type="text" x-model="dimension.unit" class="input input-bordered input-sm">
                                        </div>
                                    </template>
                                    <div>
                                        <button @click="toggleDimensionEdit(dimension)" class="btn btn-sm" x-text="dimension.editing ? 'Salvar' : 'Editar'"></button>
                                        <button @click="deleteDimension(dimension)" class="btn btn-sm btn-error">Excluir</button>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>

                <!-- Brands Tab -->
                <div x-show="activeTab === 'brands'">
                    <h2 class="card-title mb-4">Marcas</h2>
                    <form @submit.prevent="addBrand" class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label for="brand_name" class="label">
                                    <span class="label-text">Nome</span>
                                </label>
                                <input type="text" x-model="newBrand.name" id="brand_name" required class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label for="brand_logo_url" class="label">
                                    <span class="label-text">URL do Logo</span>
                                </label>
                                <input type="url" x-model="newBrand.logo_url" id="brand_logo_url" class="input input-bordered">
                            </div>
                            <div class="form-control md:col-span-2">
                                <label for="brand_description" class="label">
                                    <span class="label-text">Descrição</span>
                                </label>
                                <textarea x-model="newBrand.description" id="brand_description" rows="3" class="textarea textarea-bordered"></textarea>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                Adicionar Marca
                            </button>
                        </div>
                    </form>
                    <ul class="menu bg-base-200 w-full rounded-box">
                        <template x-for="brand in brands" :key="brand.id">
                            <li>
                                <div class="flex items-center justify-between w-full">
                                    <template x-if="!brand.editing">
                                        <div class="flex items-center">
                                            <img x-show="brand.logo_url" :src="brand.logo_url" alt="Logo" class="w-8 h-8 mr-2 object-contain">
                                            <span x-text="brand.name"></span>
                                        </div>
                                    </template>
                                    <template x-if="brand.editing">
                                        <div class="flex items-center space-x-2">
                                            <input type="text" x-model="brand.name" class="input input-bordered input-sm">
                                            <input type="url" x-model="brand.logo_url" class="input input-bordered input-sm">
                                            <textarea x-model="brand.description" class="textarea textarea-bordered textarea-sm"></textarea>
                                        </div>
                                    </template>
                                    <div>
                                        <button @click="toggleBrandEdit(brand)" class="btn btn-sm" x-text="brand.editing ? 'Salvar' : 'Editar'"></button>
                                        <button @click="deleteBrand(brand)" class="btn btn-sm btn-error">Excluir</button>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>

                <!-- Equipment Categories Tab -->
                <div x-show="activeTab === 'equipment-categories'">
                    <h2 class="card-title mb-4">Categorias de Equipamentos</h2>
                    <form @submit.prevent="addEquipmentCategory" class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label for="category_name" class="label">
                                    <span class="label-text">Nome</span>
                                </label>
                                <input type="text" x-model="newEquipmentCategory.name" id="category_name" required class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label for="category_parent_id" class="label">
                                    <span class="label-text">Categoria Pai</span>
                                </label>
                                <select x-model="newEquipmentCategory.parent_id" id="category_parent_id" class="select select-bordered w-full">
                                    <option value="">Nenhuma</option>
                                    <template x-for="category in equipmentCategories" :key="category.id">
                                        <option :value="category.id" x-text="category.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="form-control md:col-span-2">
                                <label for="category_description" class="label">
                                    <span class="label-text">Descrição</span>
                                </label>
                                <textarea x-model="newEquipmentCategory.description" id="category_description" rows="3" class="textarea textarea-bordered"></textarea>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                Adicionar Categoria de Equipamento
                            </button>
                        </div>
                    </form>
                    <ul class="menu bg-base-200 w-full rounded-box">
                        <template x-for="category in equipmentCategories" :key="category.id">
                            <li>
                                <div class="flex items-center justify-between w-full">
                                    <template x-if="!category.editing">
                                        <div>
                                            <span x-text="category.name"></span>
                                            <span x-show="category.parent_id" x-text="` (Pai: ${getCategoryParentName(category.parent_id)})`" class="text-sm text-gray-500"></span>
                                        </div>
                                    </template>
                                    <template x-if="category.editing">
                                        <div class="flex items-center space-x-2">
                                            <input type="text" x-model="category.name" class="input input-bordered input-sm">
                                            <select x-model="category.parent_id" class="select select-bordered select-sm">
                                                <option value="">Nenhuma</option>
                                                <template x-for="parentCategory in equipmentCategories.filter(c => c.id !== category.id)" :key="parentCategory.id">
                                                    <option :value="parentCategory.id" x-text="parentCategory.name"></option>
                                                </template>
                                            </select>
                                            <textarea x-model="category.description" class="textarea textarea-bordered textarea-sm"></textarea>
                                        </div>
                                    </template>
                                    <div>
                                        <button @click="toggleEquipmentCategoryEdit(category)" class="btn btn-sm" x-text="category.editing ? 'Salvar' : 'Editar'"></button>
                                        <button @click="deleteEquipmentCategory(category)" class="btn btn-sm btn-error">Excluir</button>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function settingsManager() {
    return {
        weights: @json($weights),
        dimensions: @json($dimensions),
        brands: @json($brands),
        equipmentCategories: @json($equipmentCategories),
        newWeight: { name: '', value: '', unit: '' },
        newDimension: { name: '', height: '', width: '', depth: '', unit: '' },
        newBrand: { name: '', logo_url: '', description: '' },
        newEquipmentCategory: { name: '', parent_id: '', description: '' },

        addWeight() {
            axios.post('{{ route('admin.settings.storeWeight') }}', this.newWeight)
                .then(response => {
                    this.weights.push(response.data);
                    this.newWeight = { name: '', value: '', unit: '' };
                })
                .catch(error => console.error(error));
        },

        toggleWeightEdit(weight) {
            if (weight.editing) {
                this.updateWeight(weight);
            } else {
                weight.editing = true;
            }
        },

        updateWeight(weight) {
            axios.put(`/admin/settings/weights/${weight.id}`, weight)
                .then(() => {
                    weight.editing = false;
                })
                .catch(error => console.error(error));
        },

        deleteWeight(weight) {
            if (confirm('Tem certeza que deseja excluir este peso?')) {
                axios.delete(`/admin/settings/weights/${weight.id}`)
                    .then(() => {
                        this.weights = this.weights.filter(w => w.id !== weight.id);
                    })
                    .catch(error => console.error(error));
            }
        },

        addDimension() {
            axios.post('{{ route('admin.settings.storeDimension') }}', this.newDimension)
                .then(response => {
                    this.dimensions.push(response.data);
                    this.newDimension = { name: '', height: '', width: '', depth: '', unit: '' };
                })
                .catch(error => console.error(error));
        },

        toggleDimensionEdit(dimension) {
            if (dimension.editing) {
                this.updateDimension(dimension);
            } else {
                dimension.editing = true;
            }
        },

        updateDimension(dimension) {
            axios.put(`/admin/settings/dimensions/${dimension.id}`, dimension)
                .then(() => {
                    dimension.editing = false;
                })
                .catch(error => console.error(error));
        },

        deleteDimension(dimension) {
            if (confirm('Tem certeza que deseja excluir esta dimensão?')) {
                axios.delete(`/admin/settings/dimensions/${dimension.id}`)
                    .then(() => {
                        this.dimensions = this.dimensions.filter(d => d.id !== dimension.id);
                    })
                    .catch(error => console.error(error));
            }
        },

        addBrand() {
            axios.post('{{ route('admin.settings.storeBrand') }}', this.newBrand)
                .then(response => {
                    this.brands.push(response.data);
                    this.newBrand = { name: '', logo_url: '', description: '' };
                })
                .catch(error => console.error(error));
        },

        toggleBrandEdit(brand) {
            if (brand.editing) {
                this.updateBrand(brand);
            } else {
                brand.editing = true;
            }
        },

        updateBrand(brand) {
            axios.put(`/admin/settings/brands/${brand.id}`, brand)
                .then(() => {
                    brand.editing = false;
                })
                .catch(error => console.error(error));
        },

        deleteBrand(brand) {
            if (confirm('Tem certeza que deseja excluir esta marca?')) {
                axios.delete(`/admin/settings/brands/${brand.id}`)
                    .then(() => {
                        this.brands = this.brands.filter(b => b.id !== brand.id);
                    })
                    .catch(error => console.error(error));
            }
        },

        addEquipmentCategory() {
            axios.post('{{ route('admin.settings.storeEquipmentCategory') }}', this.newEquipmentCategory)
                .then(response => {
                    this.equipmentCategories.push(response.data);
                    this.newEquipmentCategory = { name: '', parent_id: '', description: '' };
                })
                .catch(error => console.error(error));
        },

        toggleEquipmentCategoryEdit(category) {
            if (category.editing) {
                this.updateEquipmentCategory(category);
            } else {
                category.editing = true;
            }
        },

        updateEquipmentCategory(category) {
            axios.put(`/admin/settings/equipment-categories/${category.id}`, category)
                .then(() => {
                    category.editing = false;
                })
                .catch(error => console.error(error));
        },

        deleteEquipmentCategory(category) {
            if (confirm('Tem certeza que deseja excluir esta categoria de equipamento?')) {
                axios.delete(`/admin/settings/equipment-categories/${category.id}`)
                    .then(() => {
                        this.equipmentCategories = this.equipmentCategories.filter(c => c.id !== category.id);
                    })
                    .catch(error => console.error(error));
            }
        },

        getCategoryParentName(parentId) {
            const parent = this.equipmentCategories.find(c => c.id === parentId);
            return parent ? parent.name : '';
        }
    }
}
</script>
@endpush

