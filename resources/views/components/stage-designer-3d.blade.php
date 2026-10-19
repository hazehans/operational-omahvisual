@props([
    'order' => null,
    'screens' => [],
    'isEditable' => true,
    'saveUrl' => null,
    'canvasHeight' => '480px',
])

@php
    $stageWidth = old('stage_width_m', $order->stage_width_m ?? 10.0);
    $stageDepth = old('stage_depth_m', $order->stage_depth_m ?? 6.0);
    $stageHeight = old('stage_height_m', $order->stage_height_m ?? 0.6);
    $savedLayout = old('stage_layout_json', $order->stage_layout_json ?? null);
    $layoutJsonString = is_array($savedLayout) ? json_encode($savedLayout) : ($savedLayout ?: 'null');
    $savedImage = old('stage_layout_image', $order->stage_layout_image ?? null);
    $savedFrontImage = old('stage_layout_front_image', $order->stage_layout_front_image ?? null);
@endphp

<div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl text-slate-100" id="stageDesignerContainer">
    <!-- Top Action Toolbar -->
    <div class="px-5 py-3.5 bg-slate-950/80 border-b border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center font-bold text-sm">
                3D
            </div>
            <div>
                <div class="font-bold text-sm text-white flex items-center gap-2">
                    <span>Virtual 3D Stage & Layout LED Designer</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isEditable ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30' : 'bg-slate-700/40 text-slate-300 border border-slate-700' }}">
                        {{ $isEditable ? 'Interactive' : 'Viewer (Read-Only)' }}
                    </span>
                </div>
                <div class="text-xs text-slate-400">
                    {{ $isEditable ? 'Drag & Drop layar di panggung, pilih formasi panggung, hasil otomatis dicetak 2D di SPK' : 'Pratinjau 3D Formasi Layar & Panggung (Klik & geser untuk memutar kamera)' }}
                </div>
            </div>
        </div>

        <!-- Presets & View Controls -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Camera View Buttons -->
            <div class="inline-flex rounded-xl bg-slate-800 p-0.5 border border-slate-700 text-xs">
                <button type="button" id="btnView3D" class="px-2.5 py-1 rounded-lg font-semibold bg-indigo-600 text-white transition-all" title="Sudut Pandang 3D Perspektif Bebas">
                    🎥 3D Orbit
                </button>
                <button type="button" id="btnViewMapping" class="px-2.5 py-1 rounded-lg font-semibold text-slate-300 hover:text-white transition-all" title="Diagram Pemetaan Panel & Modul LED (Resolume & VJ Mapping)">
                    🔲 Mapping Panel
                </button>
                <button type="button" id="btnViewFront" class="px-2.5 py-1 rounded-lg font-semibold text-slate-300 hover:text-white transition-all" title="Tampak Depan Penonton (Front Elevation)">
                    🎭 2D Penonton
                </button>
            </div>

            @if($isEditable)
                <!-- 1-Click Formasi Presets -->
                <div class="inline-flex rounded-xl bg-slate-800 p-0.5 border border-slate-700 text-xs">
                    <button type="button" id="btnPresetCenter" class="px-2.5 py-1 rounded-lg font-semibold text-slate-300 hover:text-white hover:bg-slate-700 transition-all" title="Formasi 1 Layar Tunggal di Tengah Panggung">
                        🎪 Tengah Tunggal
                    </button>
                    <button type="button" id="btnPresetWings" class="px-2.5 py-1 rounded-lg font-semibold text-slate-300 hover:text-white hover:bg-slate-700 transition-all" title="Formasi 1 Main + 2 Wings Miring (Konser / Seminar / Wedding)">
                        🦅 Main + 2 Wings
                    </button>
                    <button type="button" id="btnPresetTriptych" class="px-2.5 py-1 rounded-lg font-semibold text-slate-300 hover:text-white hover:bg-slate-700 transition-all" title="Formasi 3 Layar Berjajar Rata (Triptych)">
                        🖼️ Triptych 3 Layar
                    </button>
                </div>

                <!-- 🧲 Snap Magnet Action -->
                <div class="inline-flex rounded-xl bg-slate-800 p-0.5 border border-slate-700 text-xs items-center">
                    <button type="button" id="btnToggleSnap" class="px-2.5 py-1 rounded-lg font-semibold bg-emerald-600 text-white transition-all flex items-center gap-1.5 shadow-xs" title="Klik untuk mengaktifkan / mematikan snap magnetik">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        <span id="snapToggleLabel">🧲 Snap: ON</span>
                    </button>
                    <select id="selectSnapStep" class="bg-slate-900 text-slate-300 text-xs rounded-r-lg px-2 py-1 border-0 focus:ring-0 cursor-pointer font-medium" title="Pilihan interval snap grid">
                        <option value="0.5">0.5m (Kabinet 50cm)</option>
                        <option value="0.25">0.25m (Presisi Halus)</option>
                        <option value="1.0">1.0m (1 Meter)</option>
                    </select>
                </div>

                @if($saveUrl)
                    <button type="button" id="btnSaveStageLayoutAjax" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30 flex items-center gap-1.5 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        <span>Simpan Layout 3D & Blueprint 2D</span>
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Main 3D Canvas Area & Stage Dimension Bar -->
    <div class="relative w-full" style="height: {{ $canvasHeight }};">
        <div id="threejsStageCanvas" class="w-full h-full cursor-grab active:cursor-grabbing"></div>

        <!-- Floating Snap Action HUD / Magnet Feedback -->
        <div id="snapFeedbackHud" class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-slate-950/90 border border-emerald-500/50 text-emerald-400 font-mono text-[11px] font-bold px-3.5 py-1 rounded-full shadow-2xl backdrop-blur-md opacity-0 transition-opacity duration-150 pointer-events-none flex items-center gap-2 z-20">
            <span class="text-sm">🧲</span>
            <span id="snapFeedbackText">Snap: Tengah Panggung (X = 0.00m)</span>
        </div>

        <!-- Floating Overlay Info (Meters scale indicator) -->
        <div class="absolute top-3 left-3 bg-slate-950/75 backdrop-blur-md px-3 py-2 rounded-xl border border-slate-800 text-[11px] space-y-1 pointer-events-none">
            <div class="font-bold text-slate-200 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Dimensi Panggung: <span id="overlayStageDim">{{ $stageWidth }}m × {{ $stageDepth }}m</span></span>
            </div>
            <div class="text-[10px] text-slate-400">
                Grid: Kotak 1m × 1m • Garis Kuning: Bibir Panggung (Audience)
            </div>
        </div>

        <!-- Floating Tooltip / Click Instructions -->
        <div class="absolute bottom-3 left-3 bg-slate-950/80 backdrop-blur-md px-3 py-1.5 rounded-xl border border-slate-800 text-[11px] text-slate-300 flex items-center gap-2">
            @if($isEditable)
                <span>🖱️ Geser layar di panggung (Snap Otomatis Aktif)</span>
                <span class="text-slate-500">•</span>
                <span>Tahan <kbd class="px-1 py-0.5 rounded bg-slate-800 text-[10px] font-mono text-amber-400 border border-slate-700">Shift</kbd> untuk drag bebas</span>
            @else
                <span>🖱️ Klik & geser untuk memutar sudut pandang 3D (Read-Only)</span>
            @endif
        </div>


        @if($isEditable)
            <!-- Selected Screen Inspector Floating Widget (Panel CRUD) -->
            <div id="screenInspectorBox" class="absolute top-14 right-3 max-h-[calc(100%-4.25rem)] overflow-y-auto bg-slate-950/95 backdrop-blur-md p-3.5 rounded-xl border border-slate-700 text-xs w-72 space-y-2.5 shadow-2xl transition-all hidden z-30">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <div class="font-bold text-white flex items-center gap-1.5 truncate flex-1 mr-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>
                        <input type="text" id="inspectScreenNameInput" value="Main Screen" class="w-full px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-xs font-bold text-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <button type="button" id="btnCloseInspector" class="text-slate-400 hover:text-white font-bold text-xs p-1" title="Tutup Inspector">✕</button>
                </div>

                <div class="space-y-2 text-[11px]">
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Dimensi Layar:</span>
                        <div class="flex items-center gap-1 text-slate-200">
                            <input type="number" step="0.5" min="0.5" max="30" id="inspectScreenWidthInput" value="6.0" class="w-14 px-1.5 py-0.5 rounded bg-slate-900 border border-slate-700 text-xs font-bold text-center text-white" title="Lebar (m)">
                            <span>×</span>
                            <input type="number" step="0.5" min="0.5" max="20" id="inspectScreenHeightInput" value="3.0" class="w-14 px-1.5 py-0.5 rounded bg-slate-900 border border-slate-700 text-xs font-bold text-center text-white" title="Tinggi (m)">
                            <span class="text-[10px] text-slate-400">m</span>
                        </div>
                    </div>
                    
                    <!-- Pos X Slider -->
                    <div>
                        <div class="flex justify-between text-slate-400 mb-0.5">
                            <span>Geser Kiri / Kanan (X):</span>
                            <span class="font-bold text-indigo-400" id="inspectPosXVal">0.0m</span>
                        </div>
                        <input type="range" id="inspectPosX" min="-6" max="6" step="0.1" value="0" class="w-full accent-indigo-500">
                    </div>

                    <!-- Pos Z Slider (Maju / Mundur) -->
                    <div>
                        <div class="flex justify-between text-slate-400 mb-0.5">
                            <span>Maju / Mundur (Z):</span>
                            <span class="font-bold text-indigo-400" id="inspectPosZVal">-1.5m</span>
                        </div>
                        <input type="range" id="inspectPosZ" min="-4" max="2" step="0.1" value="-1.5" class="w-full accent-indigo-500">
                    </div>

                    <!-- Rotation Y (Wing Angle) -->
                    <div>
                        <div class="flex justify-between text-slate-400 mb-0.5">
                            <span>Kemiringan Sayap (Rotasi):</span>
                            <span class="font-bold text-amber-400" id="inspectRotYVal">0°</span>
                        </div>
                        <input type="range" id="inspectRotY" min="-45" max="45" step="5" value="0" class="w-full accent-amber-500">
                    </div>

                    <!-- Rigging Type Toggle -->
                    <div class="pt-1 border-t border-slate-800 flex items-center justify-between">
                        <span class="text-slate-400">Metode Pasang:</span>
                        <select id="inspectRigging" class="bg-slate-900 border border-slate-700 rounded px-2 py-0.5 text-slate-200 text-[10px]">
                            <option value="ground_stake">Ground Stack (Lantai)</option>
                            <option value="hanging">Hanging (Truss Gantung)</option>
                        </select>
                    </div>

                    <!-- Delete Screen Button (Panel CRUD) -->
                    <div class="pt-2 border-t border-slate-800">
                        <button type="button" id="btnDeleteScreen" class="w-full py-1.5 px-3 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-md shadow-rose-900/40 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus Panel Layar Ini</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($isEditable)
        <!-- Bottom Stage Configuration & 2D Blueprint Preview Bar -->
        <div class="p-4 bg-slate-950 border-t border-slate-800 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            <!-- Stage Dimensions Inputs (Supports 0 for flat indoor ground/floor) -->
            <div class="space-y-1">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Ukuran Panggung Lapangan (Meter)</label>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <div class="text-[9px] text-slate-500 mb-0.5">Lebar (0 = Tanpa)</div>
                        <input type="number" step="0.5" min="0" max="50" id="inputStageWidth" name="stage_width_m" value="{{ $stageWidth }}"
                            class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs font-bold text-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <div class="text-[9px] text-slate-500 mb-0.5">Kedalaman (Maju)</div>
                        <input type="number" step="0.5" min="0" max="30" id="inputStageDepth" name="stage_depth_m" value="{{ $stageDepth }}"
                            class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs font-bold text-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <div class="text-[9px] text-slate-500 mb-0.5">Tinggi Level</div>
                        <input type="number" step="0.1" min="0" max="5" id="inputStageHeight" name="stage_height_m" value="{{ $stageHeight }}"
                            class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs font-bold text-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Live 2D Blueprint Snapshot Previews (Tampak Atas with Compass & Tampak Depan for SPK Printout) -->
            <div class="space-y-1">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="font-bold uppercase tracking-wider text-slate-400">Blueprint 2D SPK (Panel & Depan)</span>
                    <span class="text-[10px] text-emerald-400 font-semibold" id="blueprintStatus">✓ 2 Gambar Siap</span>
                </div>
                <div class="grid grid-cols-2 gap-2 h-16">
                    <!-- Pemetaan Panel & Modul LED (Panel Grid & VJ Mapping) -->
                    <div class="bg-slate-900 rounded-lg border border-slate-800 p-1 flex flex-col items-center justify-center overflow-hidden relative">
                        <span class="absolute top-1 left-1 text-[8px] bg-slate-950/85 px-1 rounded text-cyan-400 font-bold z-10 border border-slate-800">🔲 Mapping Panel</span>
                        <img id="blueprintThumbnail" src="{{ $savedImage ?: '' }}" alt="Blueprint Pemetaan Panel LED" class="max-h-full object-contain rounded {{ $savedImage ? '' : 'hidden' }}">
                        <div id="blueprintPlaceholder" class="text-[9px] text-slate-500 text-center {{ $savedImage ? 'hidden' : '' }}">
                            Mapping Panel
                        </div>
                    </div>
                    <!-- Tampak Depan (Front Elevation) without Compass -->
                    <div class="bg-slate-900 rounded-lg border border-slate-800 p-1 flex flex-col items-center justify-center overflow-hidden relative">
                        <span class="absolute top-1 left-1 text-[8px] bg-slate-950/85 px-1 rounded text-amber-400 font-bold z-10 border border-slate-800">Tampak Depan</span>
                        <img id="blueprintFrontThumbnail" src="{{ $savedFrontImage ?: '' }}" alt="Blueprint Tampak Depan" class="max-h-full object-contain rounded {{ $savedFrontImage ? '' : 'hidden' }}">
                        <div id="blueprintFrontPlaceholder" class="text-[9px] text-slate-500 text-center {{ $savedFrontImage ? 'hidden' : '' }}">
                            Elevasi Depan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Panel & Screen Action Buttons (CRUD) -->
            <div class="flex flex-col justify-end space-y-1.5">
                <div class="text-[11px] text-slate-400 flex items-center justify-between">
                    <span>Total Layar Aktif di Panggung:</span>
                    <span class="font-bold text-indigo-400" id="stageScreenCountBadge">1 Layar</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" id="btnAddCustomScreen" class="py-1.5 px-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-xs font-semibold text-white transition-all flex items-center justify-center gap-1 shadow-xs" title="Tambah Panel Baru">
                        <span>+ Panel</span>
                    </button>
                    <button type="button" id="btnAddWingScreen" class="py-1.5 px-2.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-semibold text-slate-200 transition-all flex items-center justify-center gap-1" title="Tambah Layar Sayap (Wing)">
                        <span>+ Sayap</span>
                    </button>
                    <button type="button" id="btnRefreshBlueprint" class="py-1.5 px-2 rounded-lg bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 text-xs font-semibold text-indigo-300" title="Render Ulang Blueprint 2D">
                        🔄 Render
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Hidden Inputs to bind with Laravel Form -->
    <input type="hidden" name="stage_layout_json" id="inputStageLayoutJson" value="{{ $layoutJsonString }}">
    <input type="hidden" name="stage_layout_image" id="inputStageLayoutImage" value="{{ $savedImage }}">
    <input type="hidden" name="stage_layout_front_image" id="inputStageLayoutFrontImage" value="{{ $savedFrontImage }}">
</div>

<!-- Three.js Libraries (Local with fallback to CDN) -->
<script src="{{ asset('vendor/three/three.min.js') }}"></script>
<script>
    if (typeof THREE === 'undefined') {
        document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"><\/script>');
    }
</script>
<script src="{{ asset('vendor/three/OrbitControls.js') }}"></script>
<script>
    if (typeof THREE.OrbitControls === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"><\/script>');
    }
</script>

<!-- STAGE DESIGNER 3D & 2D ENGINE SCRIPT -->
<script>
(function() {
    const isEditable = {{ $isEditable ? 'true' : 'false' }};
    let container, canvasDiv, renderer, scene, camera, controls;
    let stageMesh, stageGrid, stageEdgeBorder, trussGroup;
    let screenObjects = [];
    let selectedScreen = null;
    let raycaster, mouse;
    let isDragging = false;
    let dragPlane;
    let planeIntersect = new THREE.Vector3();

    // Initial State & Config
    let stageConfig = {
        width: parseFloat("{{ $stageWidth }}") || 10.0,
        depth: parseFloat("{{ $stageDepth }}") || 6.0,
        height: parseFloat("{{ $stageHeight }}") || 0.6
    };

    // 🧲 Snap Magnet Action Config
    let snapConfig = {
        enabled: true,
        step: 0.5, // 0.5 meter (standard 50cm LED cabinet)
        centerTolerance: 0.25, // meters to snap to center line X=0
        alignTolerance: 0.25  // meters to align with other screens
    };
    let snapCenterGuideMesh = null;
    let snapHorizGuideMesh = null;

    // Screens Data list (initial from JSON or order screens)
    let initialSavedJson = {!! $layoutJsonString !!};
    let currentScreens = [];

    if (Array.isArray(initialSavedJson) && initialSavedJson.length > 0) {
        currentScreens = initialSavedJson;
    } else {
        @if(!empty($screens) && count($screens) > 0)
            @foreach($screens as $index => $sc)
                currentScreens.push({
                    id: {{ $sc->id ?? $index }},
                    name: "{{ $sc->screen_name ?? 'Screen '.($index+1) }}",
                    width: parseFloat("{{ $sc->width_m ?? 6.0 }}") || 6.0,
                    height: parseFloat("{{ $sc->height_m ?? 3.0 }}") || 3.0,
                    x: {{ $index === 0 ? 0.0 : ($index === 1 ? -4.0 : 4.0) }},
                    z: {{ $index === 0 ? -1.5 : -1.0 }},
                    rotY: {{ $index === 1 ? 25 : ($index === 2 ? -25 : 0) }},
                    rigging: "{{ $sc->rigging_method ?? 'ground_stake' }}"
                });
            @endforeach
        @else
            currentScreens.push({
                id: 1,
                name: "Main Screen",
                width: 6.0,
                height: 3.0,
                x: 0.0,
                z: -1.5,
                rotY: 0,
                rigging: "ground_stake"
            });
        @endif
    }

    function init() {
        canvasDiv = document.getElementById('threejsStageCanvas');
        if (!canvasDiv) return;

        const width = canvasDiv.clientWidth || 800;
        const height = canvasDiv.clientHeight || 480;

        // 1. Scene
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0x0f172a); // Slate-900

        // 2. Camera
        camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 100);
        camera.position.set(0, 7, 12);

        // 3. Renderer with antialiasing and preserveDrawingBuffer for 2D capture
        renderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(window.devicePixelRatio || 1);
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        canvasDiv.appendChild(renderer.domElement);

        // 4. OrbitControls
        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.08;
        controls.maxPolarAngle = Math.PI / 2 - 0.02; // Don't go below floor
        controls.minDistance = 3;
        controls.maxDistance = 35;
        controls.target.set(0, 1.2, 0);

        // 5. Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.65);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.85);
        dirLight.position.set(5, 12, 8);
        dirLight.castShadow = true;
        dirLight.shadow.mapSize.width = 1024;
        dirLight.shadow.mapSize.height = 1024;
        scene.add(dirLight);

        // Blue/Cyan subtle stage wash light
        const stageLightLeft = new THREE.PointLight(0x38bdf8, 0.6, 25);
        stageLightLeft.position.set(-6, 5, 2);
        scene.add(stageLightLeft);

        const stageLightRight = new THREE.PointLight(0x818cf8, 0.6, 25);
        stageLightRight.position.set(6, 5, 2);
        scene.add(stageLightRight);

        // 6. Ground Floor Grid
        const groundGeo = new THREE.PlaneGeometry(30, 30);
        const groundMat = new THREE.MeshStandardMaterial({ color: 0x090d16, roughness: 0.9 });
        const groundMesh = new THREE.Mesh(groundGeo, groundMat);
        groundMesh.rotation.x = -Math.PI / 2;
        groundMesh.position.y = -0.01;
        groundMesh.receiveShadow = true;
        scene.add(groundMesh);

        const groundGrid = new THREE.GridHelper(30, 30, 0x1e293b, 0x141e33);
        groundGrid.position.y = 0.005;
        scene.add(groundGrid);

        // Invisible plane for dragging raycaster
        const dragPlaneGeo = new THREE.PlaneGeometry(100, 100);
        const dragPlaneMat = new THREE.MeshBasicMaterial({ visible: false });
        dragPlane = new THREE.Mesh(dragPlaneGeo, dragPlaneMat);
        dragPlane.rotation.x = -Math.PI / 2;
        scene.add(dragPlane);

        raycaster = new THREE.Raycaster();
        mouse = new THREE.Vector2();

        // 7. Build 3D Stage & Rigging
        buildStage();

        // 8. Build LED Screens
        buildScreens();

        // 9. Attach Event Listeners
        window.addEventListener('resize', onWindowResize);
        if (isEditable) {
            renderer.domElement.addEventListener('pointerdown', onPointerDown);
            renderer.domElement.addEventListener('pointermove', onPointerMove);
            window.addEventListener('pointerup', onPointerUp);
        }

        setupControlsUI();

        // Render loop
        animate();

        // Generate initial 2D blueprint if none exists
        setTimeout(generate2DBlueprint, 600);
    }

    // Build the 3D Stage Platform with 1-meter grid and Front indicator
    function buildStage() {
        if (stageMesh) scene.remove(stageMesh);
        if (stageGrid) scene.remove(stageGrid);
        if (stageEdgeBorder) scene.remove(stageEdgeBorder);
        if (trussGroup) scene.remove(trussGroup);

        const w = stageConfig.width;
        const d = stageConfig.depth;
        const h = stageConfig.height;

        if (w > 0 && d > 0) {
            // Stage Platform Box (minimum effective height 0.02 to avoid zero-height box geometry)
            const effH = Math.max(h, 0.02);
            const stageGeo = new THREE.BoxGeometry(w, effH, d);
            const stageMat = new THREE.MeshStandardMaterial({
                color: 0x1e293b,
                roughness: 0.4,
                metalness: 0.2
            });
            stageMesh = new THREE.Mesh(stageGeo, stageMat);
            stageMesh.position.set(0, effH / 2, 0);
            stageMesh.receiveShadow = true;
            stageMesh.castShadow = true;
            scene.add(stageMesh);

            // 1m x 1m Stage Floor Grid Lines
            stageGrid = new THREE.GridHelper(Math.max(w, d), Math.round(Math.max(w, d)), 0x475569, 0x334155);
            stageGrid.position.set(0, effH + 0.005, 0);
            scene.add(stageGrid);

            // Front Edge Border (Bibir Panggung - Audience marker) - Yellow Glow
            const edgeGeo = new THREE.BoxGeometry(w, 0.05, 0.1);
            const edgeMat = new THREE.MeshBasicMaterial({ color: 0xfacc15 }); // Amber/Yellow
            stageEdgeBorder = new THREE.Mesh(edgeGeo, edgeMat);
            stageEdgeBorder.position.set(0, effH + 0.025, d / 2);
            scene.add(stageEdgeBorder);

            // Backdrop Truss Structure behind the stage
            trussGroup = new THREE.Group();
            const trussMat = new THREE.MeshStandardMaterial({ color: 0x94a3b8, metalness: 0.8, roughness: 0.2 });

            // Left & Right upright pillars
            const pillarGeo = new THREE.CylinderGeometry(0.08, 0.08, 6, 8);
            const leftPillar = new THREE.Mesh(pillarGeo, trussMat);
            leftPillar.position.set(-w / 2, 3, -d / 2);
            trussGroup.add(leftPillar);

            const rightPillar = new THREE.Mesh(pillarGeo, trussMat);
            rightPillar.position.set(w / 2, 3, -d / 2);
            trussGroup.add(rightPillar);

            // Top horizontal truss bar
            const topBarGeo = new THREE.CylinderGeometry(0.08, 0.08, w, 8);
            const topBar = new THREE.Mesh(topBarGeo, trussMat);
            topBar.rotation.z = Math.PI / 2;
            topBar.position.set(0, 5.9, -d / 2);
            trussGroup.add(topBar);

            scene.add(trussGroup);
        } else {
            // Tanpa Panggung Khusus (Flat Ground Level / Ballroom Lantai Dasar)
            stageGrid = new THREE.GridHelper(12, 12, 0x475569, 0x1e293b);
            stageGrid.position.set(0, 0.005, 0);
            scene.add(stageGrid);
        }

        // Snap Guide Visuals (Center cyan line & Horizontal emerald line)
        if (snapCenterGuideMesh) scene.remove(snapCenterGuideMesh);
        if (snapHorizGuideMesh) scene.remove(snapHorizGuideMesh);

        const effGuideH = Math.max(h, 0);
        const guideW = w > 0 ? w : 10;
        const guideD = d > 0 ? d : 6;

        const centerPoints = [
            new THREE.Vector3(0, effGuideH + 0.02, -guideD / 2),
            new THREE.Vector3(0, effGuideH + 0.02, guideD / 2)
        ];
        const centerGeo = new THREE.BufferGeometry().setFromPoints(centerPoints);
        const centerMat = new THREE.LineBasicMaterial({ color: 0x06b6d4, linewidth: 3 });
        snapCenterGuideMesh = new THREE.Line(centerGeo, centerMat);
        snapCenterGuideMesh.visible = false;
        scene.add(snapCenterGuideMesh);

        const horizPoints = [
            new THREE.Vector3(-guideW / 2, effGuideH + 0.02, 0),
            new THREE.Vector3(guideW / 2, effGuideH + 0.02, 0)
        ];
        const horizGeo = new THREE.BufferGeometry().setFromPoints(horizPoints);
        const horizMat = new THREE.LineBasicMaterial({ color: 0x10b981, linewidth: 3 });
        snapHorizGuideMesh = new THREE.Line(horizGeo, horizMat);
        snapHorizGuideMesh.visible = false;
        scene.add(snapHorizGuideMesh);

        // Update drag plane height to top of stage
        dragPlane.position.y = Math.max(h, 0);
    }

    // Palettes matching reference image (Blue Central, Red Wing, Green Wing, Purple Mod-4, Amber, Cyan)
    const SCREEN_PALETTES = [
        { name: 'blue', even: '#1e3a8a', odd: '#2563eb', border: '#3b82f6', badge: '#38bdf8', text: '#ffffff' },
        { name: 'red', even: '#991b1b', odd: '#dc2626', border: '#ef4444', badge: '#f87171', text: '#ffffff' },
        { name: 'green', even: '#166534', odd: '#16a34a', border: '#22c55e', badge: '#4ade80', text: '#ffffff' },
        { name: 'purple', even: '#6b21a8', odd: '#9333ea', border: '#a855f7', badge: '#c084fc', text: '#ffffff' },
        { name: 'amber', even: '#9a3412', odd: '#ea580c', border: '#f97316', badge: '#fb923c', text: '#ffffff' },
        { name: 'cyan', even: '#115e59', odd: '#0d9488', border: '#14b8a6', badge: '#2dd4bf', text: '#ffffff' }
    ];

    // Helper to generate dynamic canvas texture with LED numbered cabinet grid & mapping overlays
    function createScreenTexture(name, width, height, idx = 0) {
        const canvas = document.createElement('canvas');
        canvas.width = 1024;
        canvas.height = 512;
        const ctx = canvas.getContext('2d');

        const palette = SCREEN_PALETTES[idx % SCREEN_PALETTES.length];
        const cols = Math.max(1, Math.round(width / 0.5));
        const rows = Math.max(1, Math.round(height / 0.5));
        const cellW = canvas.width / cols;
        const cellH = canvas.height / rows;

        // P3.9 Resolution (128x128 px per 50x50cm cabinet)
        const resW = cols * 128;
        const resH = rows * 128;

        // 1. Checkered numbered panels
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const pNum = r * cols + c + 1;
                ctx.fillStyle = (c + r) % 2 === 0 ? palette.even : palette.odd;
                ctx.fillRect(c * cellW, r * cellH, cellW, cellH);

                // Subtle inner cabinet border
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.18)';
                ctx.lineWidth = 1;
                ctx.strokeRect(c * cellW, r * cellH, cellW, cellH);

                // Sequential panel number (01, 02, ... 120)
                const numStr = pNum < 10 ? '0' + pNum : '' + pNum;
                const fontSize = Math.max(10, Math.min(cellW * 0.36, cellH * 0.42, 24));
                ctx.fillStyle = 'rgba(255, 255, 255, 0.72)';
                ctx.font = `bold ${fontSize}px Arial, sans-serif`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(numStr, c * cellW + cellW / 2, r * cellH + cellH / 2);
            }
        }

        // 2. Diagonal Dashed Guide Lines ('X' through center)
        ctx.save();
        ctx.setLineDash([8, 8]);
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.45)';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(0, 0); ctx.lineTo(canvas.width, canvas.height);
        ctx.moveTo(0, canvas.height); ctx.lineTo(canvas.width, 0);
        ctx.stroke();

        // 3. Center Crosshair (Horizontal & Vertical)
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.45)';
        ctx.beginPath();
        ctx.moveTo(0, canvas.height / 2); ctx.lineTo(canvas.width, canvas.height / 2);
        ctx.moveTo(canvas.width / 2, 0); ctx.lineTo(canvas.width / 2, canvas.height);
        ctx.stroke();

        // Center reticle ring
        ctx.beginPath();
        ctx.arc(canvas.width / 2, canvas.height / 2, Math.min(48, cellH * 0.8), 0, Math.PI * 2);
        ctx.stroke();
        ctx.restore();

        // 4. Outer Boundary Dashed White Border
        ctx.save();
        ctx.setLineDash([8, 6]);
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 4;
        ctx.strokeRect(3, 3, canvas.width - 6, canvas.height - 6);
        ctx.restore();

        // 5. Top-Right Corner Pitch & Module Spec Badge ([P3.9] 50 x 50 cm)
        const badgeW = 160;
        const badgeH = 30;
        const badgeX = canvas.width - badgeW - 12;
        const badgeY = 12;

        ctx.fillStyle = 'rgba(0, 0, 0, 0.85)';
        ctx.beginPath();
        if (ctx.roundRect) {
            ctx.roundRect(badgeX, badgeY, badgeW, badgeH, 6);
        } else {
            ctx.rect(badgeX, badgeY, badgeW, badgeH);
        }
        ctx.fill();
        ctx.strokeStyle = '#22c55e';
        ctx.lineWidth = 1;
        ctx.stroke();

        // Green pill inside for P3.9
        ctx.fillStyle = '#22c55e';
        ctx.beginPath();
        if (ctx.roundRect) {
            ctx.roundRect(badgeX + 4, badgeY + 4, 52, badgeH - 8, 4);
        } else {
            ctx.rect(badgeX + 4, badgeY + 4, 52, badgeH - 8);
        }
        ctx.fill();

        ctx.fillStyle = '#052e16';
        ctx.font = 'bold 12px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('P3.9', badgeX + 30, badgeY + badgeH / 2);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 12px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('50 × 50 cm', badgeX + 106, badgeY + badgeH / 2);

        // 6. Center Big Screen Name
        const cleanName = (name || 'SCREEN').toUpperCase();
        ctx.save();
        ctx.font = '900 48px Arial Black, Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.shadowColor = 'rgba(0, 0, 0, 0.9)';
        ctx.shadowBlur = 12;
        ctx.shadowOffsetX = 2;
        ctx.shadowOffsetY = 3;
        ctx.fillStyle = '#ffffff';
        ctx.fillText(cleanName, canvas.width / 2, canvas.height / 2 - 18);
        ctx.restore();

        // 7. Center Resolution Pill Badge (e.g. W 1920 x H 1024)
        const resText = `W ${resW} x H ${resH}`;
        ctx.font = 'bold 20px Arial, sans-serif';
        const resMetrics = ctx.measureText(resText);
        const resPillW = resMetrics.width + 36;
        const resPillH = 34;
        const resPillX = canvas.width / 2 - resPillW / 2;
        const resPillY = canvas.height / 2 + 16;

        ctx.fillStyle = 'rgba(0, 0, 0, 0.85)';
        ctx.beginPath();
        if (ctx.roundRect) {
            ctx.roundRect(resPillX, resPillY, resPillW, resPillH, 17);
        } else {
            ctx.rect(resPillX, resPillY, resPillW, resPillH);
        }
        ctx.fill();
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.35)';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        ctx.fillStyle = '#f8fafc';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(resText, canvas.width / 2, resPillY + resPillH / 2);

        // 8. Watermark at Bottom
        ctx.fillStyle = 'rgba(255, 255, 255, 0.35)';
        ctx.font = 'bold 11px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'alphabetic';
        ctx.fillText('OMAH VISUAL • LED DISPLAY MAPPING', canvas.width / 2, canvas.height - 10);

        return new THREE.CanvasTexture(canvas);
    }

    // Build the 3D meshes for all screens in currentScreens list
    function buildScreens() {
        // Clear existing screens from scene
        screenObjects.forEach(obj => scene.remove(obj.group));
        screenObjects = [];

        const stageY = stageConfig.height;

        currentScreens.forEach((sc, idx) => {
            const group = new THREE.Group();
            group.position.set(sc.x, stageY, sc.z);
            group.rotation.y = (sc.rotY || 0) * (Math.PI / 180);

            // Screen box geometry
            const depth = 0.12; // 12cm cabinet thickness
            const geo = new THREE.BoxGeometry(sc.width, sc.height, depth);

            // Materials for 6 faces: Right, Left, Top, Bottom, Front, Back
            const sideMat = new THREE.MeshStandardMaterial({ color: 0x111827, roughness: 0.6, metalness: 0.5 });
            const backMat = new THREE.MeshStandardMaterial({ color: 0x1f2937, roughness: 0.8 });
            const frontTex = createScreenTexture(sc.name, sc.width, sc.height, idx);
            const frontMat = new THREE.MeshStandardMaterial({
                map: frontTex,
                roughness: 0.3,
                metalness: 0.2,
                emissive: 0x1e293b,
                emissiveIntensity: 0.3
            });

            const materials = [sideMat, sideMat, sideMat, sideMat, frontMat, backMat];
            const screenMesh = new THREE.Mesh(geo, materials);

            // Elevation: center is at height / 2
            const isHanging = sc.rigging === 'hanging';
            const elevationY = isHanging ? (sc.height / 2 + 1.2) : (sc.height / 2);
            screenMesh.position.y = elevationY;
            screenMesh.castShadow = true;
            screenMesh.receiveShadow = true;

            // Highlight outline mesh (initially invisible)
            const outlineGeo = new THREE.BoxGeometry(sc.width + 0.08, sc.height + 0.08, depth + 0.08);
            const outlineMat = new THREE.MeshBasicMaterial({ color: 0x22d3ee, wireframe: true, visible: false });
            const outlineMesh = new THREE.Mesh(outlineGeo, outlineMat);
            outlineMesh.position.y = elevationY;

            group.add(screenMesh);
            group.add(outlineMesh);

            // Ground Stand / Legs or Hanging Rigging Wires
            if (!isHanging) {
                // Ground support legs behind
                const legGeo = new THREE.CylinderGeometry(0.04, 0.04, 1.2, 6);
                const legMat = new THREE.MeshStandardMaterial({ color: 0x475569 });
                const leg1 = new THREE.Mesh(legGeo, legMat);
                leg1.position.set(-sc.width / 4, 0.6, -depth / 2 - 0.2);
                leg1.rotation.x = 0.25;
                group.add(leg1);

                const leg2 = new THREE.Mesh(legGeo, legMat);
                leg2.position.set(sc.width / 4, 0.6, -depth / 2 - 0.2);
                leg2.rotation.x = 0.25;
                group.add(leg2);
            } else {
                // Hanging cables to truss
                const wireGeo = new THREE.CylinderGeometry(0.015, 0.015, 2.5, 4);
                const wireMat = new THREE.MeshBasicMaterial({ color: 0x94a3b8 });
                const wire1 = new THREE.Mesh(wireGeo, wireMat);
                wire1.position.set(-sc.width / 3, elevationY + sc.height / 2 + 0.8, 0);
                group.add(wire1);

                const wire2 = new THREE.Mesh(wireGeo, wireMat);
                wire2.position.set(sc.width / 3, elevationY + sc.height / 2 + 0.8, 0);
                group.add(wire2);
            }

            scene.add(group);

            screenObjects.push({
                data: sc,
                group: group,
                mesh: screenMesh,
                outline: outlineMesh
            });
        });

        updateScreenCountBadge();
    }

    function updateScreenCountBadge() {
        const badge = document.getElementById('stageScreenCountBadge');
        if (badge) {
            badge.textContent = `${currentScreens.length} Layar di Panggung`;
        }
    }

    // Raycasting & Drag and Drop Handling
    function getIntersectedScreen(event) {
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        const meshesToTest = screenObjects.map(so => so.mesh);
        const intersects = raycaster.intersectObjects(meshesToTest);

        if (intersects.length > 0) {
            const hitMesh = intersects[0].object;
            return screenObjects.find(so => so.mesh === hitMesh);
        }
        return null;
    }

    function onPointerDown(event) {
        if (!isEditable) return;
        if (event.button !== 0) return; // Only left-click drag

        const hit = getIntersectedScreen(event);
        if (hit) {
            selectScreen(hit);
            isDragging = true;
            controls.enabled = false; // Disable orbit while dragging screen

            const rect = renderer.domElement.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
            raycaster.setFromCamera(mouse, camera);

            const hits = raycaster.intersectObject(dragPlane);
            if (hits.length > 0) {
                planeIntersect.copy(hits[0].point);
            }
        } else {
            // Clicked empty space
            if (!event.target.closest('#screenInspectorBox')) {
                // Keep selection or deselect
            }
        }
    }

    // 🧲 Snapping Calculation Function (Center, Sibling Alignment, & Grid)
    function calculateSnappedPosition(newX, newZ, currentScreenObj, isShiftPressed) {
        if (!snapConfig.enabled || isShiftPressed) {
            return {
                x: newX,
                z: newZ,
                centerGuide: false,
                horizGuide: false,
                message: isShiftPressed ? '🔓 Free Drag (Shift Aktif)' : null
            };
        }

        let snappedX = newX;
        let snappedZ = newZ;
        let isCenter = false;
        let isAlignedSibling = false;
        let siblingName = '';
        const step = snapConfig.step;

        // 1. Center Line Snapping (X = 0)
        if (Math.abs(newX) <= snapConfig.centerTolerance) {
            snappedX = 0.0;
            isCenter = true;
        }

        // 2. Sibling Screen Alignment (Depth Z and Adjacent Edges)
        const otherScreens = screenObjects.filter(so => so !== currentScreenObj);
        for (const other of otherScreens) {
            const otherZ = other.data.z;
            const otherX = other.data.x;
            const otherW = other.data.width;
            const curW = currentScreenObj.data.width;

            // Depth (Z) Alignment: if close to sibling's Z, snap to exact same depth!
            if (Math.abs(newZ - otherZ) <= snapConfig.alignTolerance) {
                snappedZ = otherZ;
                isAlignedSibling = true;
                siblingName = other.data.name;
            }

            // Edge-to-edge magnetic snap: Wing touching / adjacent with 0.2m gap
            if (!isCenter) {
                const leftAdjacentTarget = otherX - (otherW / 2) - (curW / 2) - 0.2;
                if (Math.abs(newX - leftAdjacentTarget) <= snapConfig.alignTolerance) {
                    snappedX = parseFloat(leftAdjacentTarget.toFixed(2));
                    siblingName = siblingName || other.data.name;
                }
                const rightAdjacentTarget = otherX + (otherW / 2) + (curW / 2) + 0.2;
                if (Math.abs(newX - rightAdjacentTarget) <= snapConfig.alignTolerance) {
                    snappedX = parseFloat(rightAdjacentTarget.toFixed(2));
                    siblingName = siblingName || other.data.name;
                }
            }
        }

        // 3. Grid Snapping for un-snapped coordinates
        if (!isCenter && Math.abs(snappedX - newX) < 0.001) {
            snappedX = Math.round(snappedX / step) * step;
        }
        if (!isAlignedSibling && Math.abs(snappedZ - newZ) < 0.001) {
            snappedZ = Math.round(snappedZ / step) * step;
        }

        // Compose HUD message
        let msg = '';
        if (isCenter && isAlignedSibling) {
            msg = `Center (X: 0.0m) • Sejajar ${siblingName} (Z: ${snappedZ.toFixed(2)}m)`;
        } else if (isCenter) {
            msg = `Center Line Panggung (X: 0.0m, Z: ${snappedZ.toFixed(2)}m)`;
        } else if (isAlignedSibling) {
            msg = `Sejajar ${siblingName} (Z: ${snappedZ.toFixed(2)}m)`;
        } else {
            msg = `Grid ${step}m (X: ${snappedX.toFixed(2)}m, Z: ${snappedZ.toFixed(2)}m)`;
        }

        return {
            x: snappedX,
            z: snappedZ,
            centerGuide: isCenter,
            horizGuide: isAlignedSibling,
            message: msg
        };
    }

    function onPointerMove(event) {
        if (!isEditable) return;
        if (!isDragging || !selectedScreen) return;

        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        const hits = raycaster.intersectObject(dragPlane);

        if (hits.length > 0) {
            const newPos = hits[0].point;
            // Constrain screen inside stage perimeter with 0.2m margin
            const maxW = (stageConfig.width / 2) - 0.2;
            const maxD = (stageConfig.depth / 2) - 0.2;

            const clampedX = Math.max(-maxW, Math.min(maxW, newPos.x));
            const clampedZ = Math.max(-maxD, Math.min(maxD, newPos.z));

            const snapResult = calculateSnappedPosition(clampedX, clampedZ, selectedScreen, event.shiftKey);
            const finalX = Math.max(-maxW, Math.min(maxW, snapResult.x));
            const finalZ = Math.max(-maxD, Math.min(maxD, snapResult.z));

            selectedScreen.group.position.x = finalX;
            selectedScreen.group.position.z = finalZ;

            selectedScreen.data.x = parseFloat(finalX.toFixed(2));
            selectedScreen.data.z = parseFloat(finalZ.toFixed(2));

            // Dynamic Snap Visual Guides
            if (snapCenterGuideMesh) {
                snapCenterGuideMesh.visible = snapResult.centerGuide;
            }
            if (snapHorizGuideMesh) {
                snapHorizGuideMesh.visible = snapResult.horizGuide;
                if (snapResult.horizGuide) {
                    snapHorizGuideMesh.position.z = finalZ;
                }
            }

            // Floating Snap HUD Toast
            const hud = document.getElementById('snapFeedbackHud');
            const hudText = document.getElementById('snapFeedbackText');
            if (hud && hudText) {
                if (snapResult.message) {
                    hudText.textContent = snapResult.message;
                    hud.classList.remove('opacity-0');
                    hud.classList.add('opacity-100');
                } else {
                    hud.classList.remove('opacity-100');
                    hud.classList.add('opacity-0');
                }
            }

            syncInspectorValues();
        }
    }

    function onPointerUp() {
        if (isDragging) {
            isDragging = false;
            controls.enabled = true;
            if (snapCenterGuideMesh) snapCenterGuideMesh.visible = false;
            if (snapHorizGuideMesh) snapHorizGuideMesh.visible = false;
            const hud = document.getElementById('snapFeedbackHud');
            if (hud) {
                hud.classList.remove('opacity-100');
                hud.classList.add('opacity-0');
            }
            generate2DBlueprint(); // Update 2D blueprint when drag ends
        }
    }

    // Select a screen and open inspector
    function selectScreen(screenObj) {
        if (!isEditable) return;
        screenObjects.forEach(so => {
            if (so.outline) so.outline.material.visible = false;
        });

        selectedScreen = screenObj;
        if (selectedScreen && selectedScreen.outline) {
            selectedScreen.outline.material.visible = true;
        }

        const box = document.getElementById('screenInspectorBox');
        if (box && selectedScreen) {
            box.classList.remove('hidden');
            const nameInp = document.getElementById('inspectScreenNameInput');
            if (nameInp) nameInp.value = selectedScreen.data.name;
            const wInp = document.getElementById('inspectScreenWidthInput');
            if (wInp) wInp.value = selectedScreen.data.width;
            const hInp = document.getElementById('inspectScreenHeightInput');
            if (hInp) hInp.value = selectedScreen.data.height;
            syncInspectorValues();
        }
    }

    function syncInspectorValues() {
        if (!selectedScreen) return;
        const d = selectedScreen.data;
        document.getElementById('inspectPosX').value = d.x;
        document.getElementById('inspectPosXVal').textContent = `${d.x.toFixed(1)}m`;

        document.getElementById('inspectPosZ').value = d.z;
        document.getElementById('inspectPosZVal').textContent = `${d.z.toFixed(1)}m`;

        document.getElementById('inspectRotY').value = d.rotY || 0;
        document.getElementById('inspectRotYVal').textContent = `${d.rotY || 0}°`;

        document.getElementById('inspectRigging').value = d.rigging || 'ground_stake';
    }

    // Presets Implementation
    function applyPreset(presetType) {
        if (presetType === 'center') {
            // Main Screen centered
            if (currentScreens.length > 0) {
                currentScreens[0].x = 0;
                currentScreens[0].z = -stageConfig.depth / 4;
                currentScreens[0].rotY = 0;
            }
        } else if (presetType === 'wings') {
            // Main Screen + 2 Wings
            if (currentScreens.length >= 1) {
                const mainW = currentScreens[0].width;
                currentScreens[0].x = 0;
                currentScreens[0].z = -stageConfig.depth / 3;
                currentScreens[0].rotY = 0;

                // If only 1 screen, auto-create 2 wings!
                if (currentScreens.length === 1) {
                    const wingW = Math.max(2, Math.round(mainW * 0.4 * 2) / 2);
                    const wingH = currentScreens[0].height;
                    currentScreens.push({
                        id: 2,
                        name: "Wing Kiri (Cam)",
                        width: wingW,
                        height: wingH,
                        x: -(mainW / 2 + wingW / 2 + 0.3),
                        z: -stageConfig.depth / 4 + 0.4,
                        rotY: 20,
                        rigging: "ground_stake"
                    });
                    currentScreens.push({
                        id: 3,
                        name: "Wing Kanan (Cam)",
                        width: wingW,
                        height: wingH,
                        x: (mainW / 2 + wingW / 2 + 0.3),
                        z: -stageConfig.depth / 4 + 0.4,
                        rotY: -20,
                        rigging: "ground_stake"
                    });
                } else if (currentScreens.length >= 3) {
                    const wingLeft = currentScreens[1];
                    const wingRight = currentScreens[2];
                    wingLeft.x = -(mainW / 2 + wingLeft.width / 2 + 0.3);
                    wingLeft.z = -stageConfig.depth / 4 + 0.4;
                    wingLeft.rotY = 20;

                    wingRight.x = (mainW / 2 + wingRight.width / 2 + 0.3);
                    wingRight.z = -stageConfig.depth / 4 + 0.4;
                    wingRight.rotY = -20;
                }
            }
        } else if (presetType === 'triptych') {
            // 3 Screens side-by-side
            if (currentScreens.length === 1) {
                const subW = Math.round((currentScreens[0].width / 3) * 2) / 2;
                const h = currentScreens[0].height;
                currentScreens[0].width = subW;
                currentScreens[0].x = 0;
                currentScreens[0].z = -stageConfig.depth / 3;

                currentScreens.push({
                    id: 2,
                    name: "Kolom Kiri",
                    width: subW,
                    height: h,
                    x: -(subW + 0.4),
                    z: -stageConfig.depth / 3,
                    rotY: 0,
                    rigging: "ground_stake"
                });
                currentScreens.push({
                    id: 3,
                    name: "Kolom Kanan",
                    width: subW,
                    height: h,
                    x: (subW + 0.4),
                    z: -stageConfig.depth / 3,
                    rotY: 0,
                    rigging: "ground_stake"
                });
            } else if (currentScreens.length === 3) {
                currentScreens[0].x = 0;
                currentScreens[0].z = -stageConfig.depth / 3;
                currentScreens[0].rotY = 0;

                currentScreens[1].x = -(currentScreens[1].width + 0.4);
                currentScreens[1].z = -stageConfig.depth / 3;
                currentScreens[1].rotY = 0;

                currentScreens[2].x = (currentScreens[2].width + 0.4);
                currentScreens[2].z = -stageConfig.depth / 3;
                currentScreens[2].rotY = 0;
            }
        }

        buildScreens();
        generate2DBlueprint();
    }

    // Camera Views
    function setCameraView(view) {
        document.querySelectorAll('#btnView3D, #btnViewMapping, #btnViewFront').forEach(b => {
            b?.classList.remove('bg-indigo-600', 'text-white');
            b?.classList.add('text-slate-300');
        });

        if (view === '3d') {
            document.getElementById('btnView3D')?.classList.add('bg-indigo-600', 'text-white');
            camera.position.set(0, 7, 12);
            controls.target.set(0, 1.2, 0);
        } else if (view === 'mapping') {
            document.getElementById('btnViewMapping')?.classList.add('bg-indigo-600', 'text-white');
            const targetY = stageConfig.height + 1.5;
            camera.position.set(0, targetY, stageConfig.depth / 2 + 7.5);
            controls.target.set(0, targetY, 0);
        } else if (view === 'front') {
            document.getElementById('btnViewFront')?.classList.add('bg-indigo-600', 'text-white');
            camera.position.set(0, stageConfig.height + 1.5, stageConfig.depth / 2 + 8);
            controls.target.set(0, stageConfig.height + 1.5, 0);
        }
        controls.update();
    }

    // =========================================================================
    // 2D BLUEPRINT GENERATORS (CRITICAL FOR SPK PRINT & RIGGING CREW)
    // Generates both Pemetaan Panel LED (Mapping Blueprint) and Tampak Depan (Front Elevation)
    // =========================================================================

    // 1. PEMETAAN PANEL & KABINET LED (LED MAPPING BLUEPRINT - RESOLUME / NOVASTAR STYLE)
    function generate2DPanelMappingBlueprint() {
        const bpCanvas = document.createElement('canvas');
        bpCanvas.width = 1200;
        bpCanvas.height = 600;
        const ctx = bpCanvas.getContext('2d');

        // Dark Blueprint Background with subtle grid lines
        ctx.fillStyle = '#070b14';
        ctx.fillRect(0, 0, bpCanvas.width, bpCanvas.height);

        ctx.strokeStyle = '#0f172a';
        ctx.lineWidth = 1;
        for (let x = 0; x <= bpCanvas.width; x += 40) {
            ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, bpCanvas.height); ctx.stroke();
        }
        for (let y = 0; y <= bpCanvas.height; y += 40) {
            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(bpCanvas.width, y); ctx.stroke();
        }

        // Calculate Totals across all screens
        const totalCabinetsAll = currentScreens.reduce((acc, sc) => {
            const cols = Math.max(1, Math.round(sc.width / 0.5));
            const rows = Math.max(1, Math.round(sc.height / 0.5));
            return acc + (cols * rows);
        }, 0);
        const totalSqmAll = currentScreens.reduce((acc, sc) => acc + (sc.width * sc.height), 0);

        // Header Block
        ctx.fillStyle = 'rgba(15, 23, 42, 0.96)';
        ctx.fillRect(20, 16, bpCanvas.width - 40, 70);
        ctx.strokeStyle = '#38bdf8';
        ctx.lineWidth = 1.5;
        ctx.strokeRect(20, 16, bpCanvas.width - 40, 70);

        ctx.fillStyle = '#38bdf8';
        ctx.font = 'bold 16px Arial, sans-serif';
        ctx.textAlign = 'left';
        ctx.fillText('OMAH VISUAL — DIAGRAM PEMETAAN PANEL & KABINET LED (MAPPING BLUEPRINT)', 36, 40);

        ctx.fillStyle = '#e2e8f0';
        ctx.font = '13px Arial, sans-serif';
        ctx.fillText(`Total Formasi: ${currentScreens.length} Layar • ${totalCabinetsAll} Kabinet (${totalSqmAll.toFixed(1)} m²) • Standar Modul: 50 × 50 cm`, 36, 59);

        ctx.fillStyle = '#94a3b8';
        ctx.font = '12px Arial, sans-serif';
        ctx.fillText('Pitch P3.9 (128×128 px/kabinet) • Format Sinyal & VJ Mapping: Resolume Arena / NovaStar NovaLCT', 36, 76);

        // Header Right Badge
        const tagText = 'PANDUAN TEKNISI & VJ KONTEN';
        ctx.font = 'bold 11px Arial, sans-serif';
        const tagW = ctx.measureText(tagText).width + 24;
        const tagX = bpCanvas.width - tagW - 36;
        ctx.fillStyle = 'rgba(56, 189, 248, 0.15)';
        ctx.fillRect(tagX, 36, tagW, 28);
        ctx.strokeStyle = '#38bdf8';
        ctx.lineWidth = 1;
        ctx.strokeRect(tagX, 36, tagW, 28);
        ctx.fillStyle = '#38bdf8';
        ctx.textAlign = 'center';
        ctx.fillText(tagText, tagX + tagW / 2, 54);

        // Sort screens from left to right according to X position
        const sortedScreens = [...currentScreens].sort((a, b) => (a.x || 0) - (b.x || 0));

        // Layout calculation to fit screens within canvas
        const gapMeters = 0.4;
        const totalMetersW = sortedScreens.reduce((sum, sc) => sum + sc.width, 0) + (sortedScreens.length - 1) * gapMeters;
        const maxMeterH = sortedScreens.reduce((max, sc) => Math.max(max, sc.height), 1);

        const marginX = 40;
        const availW = bpCanvas.width - marginX * 2;
        const availH = 430;
        const scale = Math.min(availW / Math.max(totalMetersW, 5), availH / (maxMeterH + 0.8));

        const gapPx = 20;
        const totalScreensPxW = sortedScreens.reduce((sum, sc) => sum + (sc.width * scale), 0) + (sortedScreens.length - 1) * gapPx;
        let currentDrawX = marginX + Math.max(0, (availW - totalScreensPxW) / 2);
        const topAreaY = 102;

        sortedScreens.forEach((sc, idx) => {
            const scW_px = sc.width * scale;
            const scH_px = sc.height * scale;
            const scY = topAreaY + (availH - 55 - scH_px) / 2;

            const palette = SCREEN_PALETTES[idx % SCREEN_PALETTES.length];
            const cols = Math.max(1, Math.round(sc.width / 0.5));
            const rows = Math.max(1, Math.round(sc.height / 0.5));
            const totalPanels = cols * rows;

            const cellW = scW_px / cols;
            const cellH = scH_px / rows;

            const resW = cols * 128;
            const resH = rows * 128;

            // 1. Checkered numbered panels
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    const pNum = r * cols + c + 1;
                    const cellX = currentDrawX + c * cellW;
                    const cellY = scY + r * cellH;

                    ctx.fillStyle = (c + r) % 2 === 0 ? palette.even : palette.odd;
                    ctx.fillRect(cellX, cellY, cellW, cellH);

                    // Subtle inner cabinet border
                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.22)';
                    ctx.lineWidth = 1;
                    ctx.strokeRect(cellX, cellY, cellW, cellH);

                    // Sequential panel number (01, 02, ... 120)
                    const numStr = pNum < 10 ? '0' + pNum : '' + pNum;
                    const fontSize = Math.max(9, Math.min(cellW * 0.38, cellH * 0.42, 22));
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.78)';
                    ctx.font = `bold ${fontSize}px Arial, sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(numStr, cellX + cellW / 2, cellY + cellH / 2);
                }
            }

            // 2. Diagonal Dashed Guide Lines ('X' through screen)
            ctx.save();
            ctx.setLineDash([7, 7]);
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.45)';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(currentDrawX, scY); ctx.lineTo(currentDrawX + scW_px, scY + scH_px);
            ctx.moveTo(currentDrawX, scY + scH_px); ctx.lineTo(currentDrawX + scW_px, scY);
            ctx.stroke();

            // 3. Center Crosshair (Horizontal & Vertical)
            const midX = currentDrawX + scW_px / 2;
            const midY = scY + scH_px / 2;
            ctx.beginPath();
            ctx.moveTo(currentDrawX, midY); ctx.lineTo(currentDrawX + scW_px, midY);
            ctx.moveTo(midX, scY); ctx.lineTo(midX, scY + scH_px);
            ctx.stroke();

            // Center reticle ring
            ctx.beginPath();
            ctx.arc(midX, midY, Math.min(36, cellH * 0.75), 0, Math.PI * 2);
            ctx.stroke();
            ctx.restore();

            // 4. Outer Boundary Dashed White Border
            ctx.save();
            ctx.setLineDash([8, 6]);
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 3.5;
            ctx.strokeRect(currentDrawX, scY, scW_px, scH_px);
            ctx.restore();

            // 5. Top-Right Corner Pitch Badge ([P3.9] 50 x 50 cm)
            if (scW_px >= 120) {
                const cornerBadgeW = Math.min(135, scW_px * 0.45);
                const cornerBadgeH = 24;
                const cornerBadgeX = currentDrawX + scW_px - cornerBadgeW - 6;
                const cornerBadgeY = scY + 6;

                ctx.fillStyle = 'rgba(0, 0, 0, 0.85)';
                ctx.fillRect(cornerBadgeX, cornerBadgeY, cornerBadgeW, cornerBadgeH);
                ctx.strokeStyle = '#22c55e';
                ctx.lineWidth = 1;
                ctx.strokeRect(cornerBadgeX, cornerBadgeY, cornerBadgeW, cornerBadgeH);

                ctx.fillStyle = '#22c55e';
                ctx.fillRect(cornerBadgeX + 3, cornerBadgeY + 3, 38, cornerBadgeH - 6);
                ctx.fillStyle = '#052e16';
                ctx.font = 'bold 10px Arial, sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('P3.9', cornerBadgeX + 22, cornerBadgeY + cornerBadgeH / 2);

                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 10px Arial, sans-serif';
                ctx.fillText('50 × 50 cm', cornerBadgeX + 85, cornerBadgeY + cornerBadgeH / 2);
            }

            // 6. Big Screen Name in Center
            const cleanName = (sc.name || `SCREEN ${idx + 1}`).toUpperCase();
            ctx.save();
            const nameFontSize = Math.max(16, Math.min(scW_px * 0.12, scH_px * 0.22, 42));
            ctx.font = `900 ${nameFontSize}px Arial Black, Arial, sans-serif`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.95)';
            ctx.shadowBlur = 10;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 2;
            ctx.fillStyle = '#ffffff';
            ctx.fillText(cleanName, midX, midY - (scH_px > 100 ? 14 : 0));
            ctx.restore();

            // 7. Center Resolution Pill Badge (e.g. W 1920 x H 1024)
            if (scH_px > 90) {
                const resText = `W ${resW} x H ${resH}`;
                const resFontSz = Math.max(11, Math.min(nameFontSize * 0.48, 18));
                ctx.font = `bold ${resFontSz}px Arial, sans-serif`;
                const resMetrics = ctx.measureText(resText);
                const resPillW = resMetrics.width + 24;
                const resPillH = resFontSz + 12;
                const resPillX = midX - resPillW / 2;
                const resPillY = midY + 12;

                ctx.fillStyle = 'rgba(0, 0, 0, 0.88)';
                ctx.fillRect(resPillX, resPillY, resPillW, resPillH);
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
                ctx.lineWidth = 1;
                ctx.strokeRect(resPillX, resPillY, resPillW, resPillH);

                ctx.fillStyle = '#f8fafc';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(resText, midX, resPillY + resPillH / 2);
            }

            // 8. Info Strip Below Screen
            const infoY = scY + scH_px + 10;
            ctx.fillStyle = 'rgba(15, 23, 42, 0.9)';
            ctx.fillRect(currentDrawX, infoY, scW_px, 26);
            ctx.strokeStyle = palette.border;
            ctx.lineWidth = 1;
            ctx.strokeRect(currentDrawX, infoY, scW_px, 26);

            ctx.fillStyle = '#f8fafc';
            ctx.font = 'bold 11px Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            const infoText = `${sc.name}: ${sc.width}m × ${sc.height}m (${cols}×${rows} = ${totalPanels} Kabinet) • ${resW}×${resH} px`;
            ctx.fillText(infoText, midX, infoY + 13);

            currentDrawX += scW_px + gapPx;
        });

        // Bottom Technical Instruction Legend
        ctx.fillStyle = '#94a3b8';
        ctx.font = '11px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'alphabetic';
        ctx.fillText('ℹ️ Alur nomor kabinet: kiri-ke-kanan, atas-ke-bawah (standar NovaStar). Pastikan perkabelan data & catu daya mengikuti urutan nomor di atas.', bpCanvas.width / 2, bpCanvas.height - 14);

        // Convert to Base64 PNG Data URL
        const dataUrl = bpCanvas.toDataURL('image/png');

        // Update hidden input & thumbnail
        const inputImg = document.getElementById('inputStageLayoutImage');
        const thumb = document.getElementById('blueprintThumbnail');
        const placeholder = document.getElementById('blueprintPlaceholder');

        if (inputImg) inputImg.value = dataUrl;
        if (thumb) {
            thumb.src = dataUrl;
            thumb.classList.remove('hidden');
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }

        return dataUrl;
    }

    // 2. TAMPAK DEPAN (FRONT ELEVATION / PERSPEKTIF PENONTON)
    function generate2DFrontBlueprint() {
        const bpCanvas = document.createElement('canvas');
        bpCanvas.width = 1200;
        bpCanvas.height = 600;
        const ctx = bpCanvas.getContext('2d');

        // Dark Technical Blueprint Background
        ctx.fillStyle = '#0a0f1d';
        ctx.fillRect(0, 0, bpCanvas.width, bpCanvas.height);

        // Calculate dynamic vertical and horizontal bounds
        const maxScreenH = currentScreens.reduce((max, sc) => Math.max(max, sc.height || 3), 3);
        const hasHanging = currentScreens.some(sc => sc.rigging === 'hanging');
        const totalHM = stageConfig.height + maxScreenH + (hasHanging ? 1.4 : 0.5) + 0.6;
        const contentW = Math.max(stageConfig.width + 1.0, 6);

        const marginX = 45;
        const marginY = 35;
        const scale = Math.min((bpCanvas.width - marginX * 2) / contentW, (bpCanvas.height - marginY * 2) / totalHM);
        const centerX = bpCanvas.width / 2;
        const groundY = bpCanvas.height - 42; // Baseline floor (±0.00m) near bottom

        // Draw Stage Platform Podium Base Fill
        const stageW_px = stageConfig.width * scale;
        const stageH_px = Math.max(24, stageConfig.height * scale);
        const stageX = centerX - stageW_px / 2;
        const stageTopY = groundY - stageH_px;

        ctx.fillStyle = '#1e293b';
        ctx.fillRect(stageX, stageTopY, stageW_px, stageH_px);

        // Technical Grid (1 Meter Intervals) Covering FULL CANVAS
        ctx.strokeStyle = '#1e293b';
        ctx.lineWidth = 1;
        for (let x = centerX; x <= bpCanvas.width; x += scale) {
            ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, bpCanvas.height); ctx.stroke();
        }
        for (let x = centerX - scale; x >= 0; x -= scale) {
            ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, bpCanvas.height); ctx.stroke();
        }
        for (let y = groundY; y >= 0; y -= scale) {
            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(bpCanvas.width, y); ctx.stroke();
        }
        for (let y = groundY + scale; y <= bpCanvas.height; y += scale) {
            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(bpCanvas.width, y); ctx.stroke();
        }

        // Inside Stage: Brighter Grid Lines
        ctx.strokeStyle = '#334155';
        ctx.lineWidth = 1.5;
        for (let x = centerX; x <= stageX + stageW_px; x += scale) {
            ctx.beginPath(); ctx.moveTo(x, stageTopY); ctx.lineTo(x, groundY); ctx.stroke();
        }
        for (let x = centerX - scale; x >= stageX; x -= scale) {
            ctx.beginPath(); ctx.moveTo(x, stageTopY); ctx.lineTo(x, groundY); ctx.stroke();
        }

        // Draw Ground Level (±0.00m)
        ctx.strokeStyle = '#475569';
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.moveTo(0, groundY);
        ctx.lineTo(bpCanvas.width, groundY);
        ctx.stroke();

        ctx.fillStyle = '#cbd5e1';
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'right';
        ctx.fillText('LANTAI DASAR / GROUND LEVEL (±0.00m)', bpCanvas.width - 25, groundY + 22);

        // Stage platform outline & skirt vertical slats
        ctx.strokeStyle = '#64748b';
        ctx.lineWidth = 2.5;
        ctx.strokeRect(stageX, stageTopY, stageW_px, stageH_px);

        ctx.strokeStyle = '#334155';
        ctx.lineWidth = 1;
        for (let sx = stageX + 25; sx < stageX + stageW_px; sx += 30) {
            ctx.beginPath();
            ctx.moveTo(sx, stageTopY);
            ctx.lineTo(sx, groundY);
            ctx.stroke();
        }

        // Stage Level text
        ctx.fillStyle = '#facc15';
        ctx.font = 'bold 15px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(`LEVEL PANGGUNG (+${stageConfig.height}m) • LEBAR: ${stageConfig.width}m`, centerX, stageTopY + (stageH_px > 30 ? stageH_px / 2 + 5 : -10));

        // Center line dashed
        ctx.strokeStyle = '#38bdf8';
        ctx.lineWidth = 2;
        ctx.setLineDash([6, 6]);
        ctx.beginPath();
        ctx.moveTo(centerX, 90);
        ctx.lineTo(centerX, groundY);
        ctx.stroke();
        ctx.setLineDash([]);

        // Draw LED Screens in Front Elevation
        currentScreens.forEach((sc, idx) => {
            const rotRad = (sc.rotY || 0) * (Math.PI / 180);
            // Projected visual width from front view
            const scW_px = Math.max(50, sc.width * scale * Math.max(0.65, Math.abs(Math.cos(rotRad))));
            const scH_px = sc.height * scale;
            const scX = centerX + (sc.x * scale);

            const isHanging = sc.rigging === 'hanging';
            const clearanceM = isHanging ? 0.6 : 0.0;
            const clearance_px = clearanceM * scale;
            const scBottomY = stageTopY - clearance_px;
            const scTopY = scBottomY - scH_px;

            // Draw Rigging Hardware
            if (isHanging) {
                // Truss Beam above screen
                const trussY = Math.max(70, scTopY - 45);
                const trussW = scW_px + 40;
                ctx.fillStyle = '#334155';
                ctx.fillRect(scX - trussW / 2, trussY, trussW, 18);
                ctx.strokeStyle = '#94a3b8';
                ctx.lineWidth = 2;
                ctx.strokeRect(scX - trussW / 2, trussY, trussW, 18);

                // Truss webbing pattern
                for (let tx = scX - trussW / 2 + 10; tx < scX + trussW / 2; tx += 20) {
                    ctx.beginPath();
                    ctx.moveTo(tx, trussY);
                    ctx.lineTo(tx + 10, trussY + 18);
                    ctx.stroke();
                }

                // Hanging Wire Ropes / Hoists
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 3;
                ctx.beginPath();
                ctx.moveTo(scX - scW_px * 0.4, trussY + 18);
                ctx.lineTo(scX - scW_px * 0.4, scTopY);
                ctx.stroke();
                ctx.beginPath();
                ctx.moveTo(scX + scW_px * 0.4, trussY + 18);
                ctx.lineTo(scX + scW_px * 0.4, scTopY);
                ctx.stroke();

                ctx.fillStyle = '#94a3b8';
                ctx.font = 'bold 12px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('TRUSS RIGGING GANTUNG', scX, trussY - 7);
            } else {
                // Ground Stack Base Feet on Stage
                ctx.fillStyle = '#475569';
                ctx.fillRect(scX - scW_px * 0.45, stageTopY - 7, 24, 7);
                ctx.fillRect(scX + scW_px * 0.45 - 24, stageTopY - 7, 24, 7);
                if (scW_px > 140) {
                    ctx.fillRect(scX - 12, stageTopY - 7, 24, 7);
                }
            }

            // Screen Main Box
            ctx.fillStyle = idx === 0 ? '#1d4ed8' : '#0284c7';
            ctx.fillRect(scX - scW_px / 2, scTopY, scW_px, scH_px);
            ctx.strokeStyle = '#38bdf8';
            ctx.lineWidth = 3;
            ctx.strokeRect(scX - scW_px / 2, scTopY, scW_px, scH_px);

            // Cabinet Division Lines (0.5m grid approx)
            ctx.strokeStyle = idx === 0 ? '#1e40af' : '#0369a1';
            ctx.lineWidth = 1;
            const cabStepX = scale * 0.5;
            for (let cx = scX - scW_px / 2 + cabStepX; cx < scX + scW_px / 2; cx += cabStepX) {
                ctx.beginPath();
                ctx.moveTo(cx, scTopY);
                ctx.lineTo(cx, scBottomY);
                ctx.stroke();
            }
            for (let cy = scTopY + cabStepX; cy < scBottomY; cy += cabStepX) {
                ctx.beginPath();
                ctx.moveTo(scX - scW_px / 2, cy);
                ctx.lineTo(scX + scW_px / 2, cy);
                ctx.stroke();
            }

            // Screen Labels inside screen (BIG & BOLD)
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 18px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(sc.name, scX, scTopY + (scH_px / 2) - 16);

            ctx.fillStyle = '#fef08a';
            ctx.font = 'bold 16px Arial';
            ctx.fillText(`${sc.width}m × ${sc.height}m`, scX, scTopY + (scH_px / 2) + 6);

            ctx.fillStyle = '#38bdf8';
            ctx.font = 'bold 13px Arial';
            ctx.fillText(`${isHanging ? 'Rigging Truss Gantung' : 'Ground Stack'} • ${(sc.width * sc.height).toFixed(1)} m²`, scX, scTopY + (scH_px / 2) + 26);

            // Vertical Height Dimension Indicator
            const dimLineX = scX - scW_px / 2 - 16;
            ctx.strokeStyle = '#94a3b8';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(dimLineX, scTopY);
            ctx.lineTo(dimLineX, scBottomY);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(dimLineX - 4, scTopY); ctx.lineTo(dimLineX + 4, scTopY);
            ctx.moveTo(dimLineX - 4, scBottomY); ctx.lineTo(dimLineX + 4, scBottomY);
            ctx.stroke();

            ctx.save();
            ctx.translate(dimLineX - 8, scTopY + scH_px / 2);
            ctx.rotate(-Math.PI / 2);
            ctx.fillStyle = '#e2e8f0';
            ctx.font = 'bold 13px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(`Tinggi: ${sc.height}m`, 0, 0);
            ctx.restore();

            // Elevation Marker at Peak
            const totalPeak = (stageConfig.height + sc.height + clearanceM).toFixed(2);
            ctx.fillStyle = '#38bdf8';
            ctx.font = 'bold 14px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(`▲ Puncak: +${totalPeak}m`, scX, scTopY - 12);
        });

        // Blueprint Header / Title Block
        ctx.fillStyle = 'rgba(15, 23, 42, 0.95)';
        ctx.fillRect(20, 20, 480, 70);
        ctx.strokeStyle = '#facc15';
        ctx.lineWidth = 1.5;
        ctx.strokeRect(20, 20, 480, 70);

        ctx.fillStyle = '#facc15';
        ctx.font = 'bold 16px Arial';
        ctx.textAlign = 'left';
        ctx.fillText('OMAH VISUAL - BLUEPRINT ELEVASI TAMPAK DEPAN', 32, 44);
        ctx.fillStyle = '#e2e8f0';
        ctx.font = '13px Arial';
        ctx.fillText(`Format: Tampak Depan Penonton • Total Layar: ${currentScreens.length} Unit`, 32, 63);
        ctx.fillStyle = '#94a3b8';
        ctx.font = '12px Arial';
        ctx.fillText(`Level Panggung: +${stageConfig.height}m • Skala Dinamis: ${scale.toFixed(1)} px/m`, 32, 80);

        // Convert to Base64 PNG Data URL
        const dataUrl = bpCanvas.toDataURL('image/png');

        // Update hidden input & thumbnail
        const inputFrontImg = document.getElementById('inputStageLayoutFrontImage');
        const thumbFront = document.getElementById('blueprintFrontThumbnail');
        const placeholderFront = document.getElementById('blueprintFrontPlaceholder');

        if (inputFrontImg) inputFrontImg.value = dataUrl;
        if (thumbFront) {
            thumbFront.src = dataUrl;
            thumbFront.classList.remove('hidden');
        }
        if (placeholderFront) {
            placeholderFront.classList.add('hidden');
        }

        return dataUrl;
    }

    // Master function to generate both blueprints
    function generate2DBlueprint() {
        const topUrl = generate2DPanelMappingBlueprint();
        const frontUrl = generate2DFrontBlueprint();

        // Update screens JSON in hidden input
        const inputJson = document.getElementById('inputStageLayoutJson');
        if (inputJson) inputJson.value = JSON.stringify(currentScreens);

        const statusEl = document.getElementById('blueprintStatus');
        if (statusEl) statusEl.textContent = '✓ 2 Gambar Siap';

        return topUrl;
    }

    // Attach UI Event Listeners
    function setupControlsUI() {
        document.getElementById('btnView3D')?.addEventListener('click', () => setCameraView('3d'));
        document.getElementById('btnViewMapping')?.addEventListener('click', () => setCameraView('mapping'));
        document.getElementById('btnViewFront')?.addEventListener('click', () => setCameraView('front'));

        document.getElementById('btnPresetCenter')?.addEventListener('click', () => applyPreset('center'));
        document.getElementById('btnPresetWings')?.addEventListener('click', () => applyPreset('wings'));
        document.getElementById('btnPresetTriptych')?.addEventListener('click', () => applyPreset('triptych'));

        // 🧲 Snap Magnet Toggle & Step Selector
        const btnToggleSnap = document.getElementById('btnToggleSnap');
        const selectSnapStep = document.getElementById('selectSnapStep');
        const snapToggleLabel = document.getElementById('snapToggleLabel');

        btnToggleSnap?.addEventListener('click', function() {
            snapConfig.enabled = !snapConfig.enabled;
            if (snapConfig.enabled) {
                btnToggleSnap.classList.remove('bg-slate-700', 'text-slate-400');
                btnToggleSnap.classList.add('bg-emerald-600', 'text-white');
                snapToggleLabel.textContent = '🧲 Snap: ON';
            } else {
                btnToggleSnap.classList.remove('bg-emerald-600', 'text-white');
                btnToggleSnap.classList.add('bg-slate-700', 'text-slate-400');
                snapToggleLabel.textContent = '🔓 Snap: OFF';
            }
        });

        selectSnapStep?.addEventListener('change', function() {
            snapConfig.step = parseFloat(this.value) || 0.5;
            const inspectPosX = document.getElementById('inspectPosX');
            const inspectPosZ = document.getElementById('inspectPosZ');
            if (inspectPosX) inspectPosX.step = snapConfig.step;
            if (inspectPosZ) inspectPosZ.step = snapConfig.step;
        });

        document.getElementById('btnRefreshBlueprint')?.addEventListener('click', () => generate2DBlueprint());

        // Stage Dimension inputs
        const inpW = document.getElementById('inputStageWidth');
        const inpD = document.getElementById('inputStageDepth');
        const inpH = document.getElementById('inputStageHeight');

        function onStageDimChange() {
            stageConfig.width = parseFloat(inpW?.value) || 10.0;
            stageConfig.depth = parseFloat(inpD?.value) || 6.0;
            stageConfig.height = parseFloat(inpH?.value) || 0.6;
            document.getElementById('overlayStageDim').textContent = `${stageConfig.width}m × ${stageConfig.depth}m`;
            buildStage();
            buildScreens();
            generate2DBlueprint();
        }

        inpW?.addEventListener('change', onStageDimChange);
        inpD?.addEventListener('change', onStageDimChange);
        inpH?.addEventListener('change', onStageDimChange);

        // Inspector Controls
        document.getElementById('btnCloseInspector')?.addEventListener('click', () => {
            document.getElementById('screenInspectorBox').classList.add('hidden');
            if (selectedScreen && selectedScreen.outline) {
                selectedScreen.outline.material.visible = false;
            }
            selectedScreen = null;
        });

        document.getElementById('inspectPosX')?.addEventListener('input', (e) => {
            if (!selectedScreen) return;
            const val = parseFloat(e.target.value);
            selectedScreen.data.x = val;
            selectedScreen.group.position.x = val;
            document.getElementById('inspectPosXVal').textContent = `${val.toFixed(1)}m`;
            generate2DBlueprint();
        });

        document.getElementById('inspectPosZ')?.addEventListener('input', (e) => {
            if (!selectedScreen) return;
            const val = parseFloat(e.target.value);
            selectedScreen.data.z = val;
            selectedScreen.group.position.z = val;
            document.getElementById('inspectPosZVal').textContent = `${val.toFixed(1)}m`;
            generate2DBlueprint();
        });

        document.getElementById('inspectRotY')?.addEventListener('input', (e) => {
            if (!selectedScreen) return;
            const val = parseInt(e.target.value);
            selectedScreen.data.rotY = val;
            selectedScreen.group.rotation.y = val * (Math.PI / 180);
            document.getElementById('inspectRotYVal').textContent = `${val}°`;
            generate2DBlueprint();
        });

        document.getElementById('inspectRigging')?.addEventListener('change', (e) => {
            if (!selectedScreen) return;
            selectedScreen.data.rigging = e.target.value;
            buildScreens();
            selectScreen(screenObjects.find(so => so.data.id === selectedScreen.data.id));
            generate2DBlueprint();
        });

        // Panel CRUD: Edit Name, Width, Height
        document.getElementById('inspectScreenNameInput')?.addEventListener('input', (e) => {
            if (!selectedScreen) return;
            selectedScreen.data.name = e.target.value;
            generate2DBlueprint();
        });

        document.getElementById('inspectScreenWidthInput')?.addEventListener('change', (e) => {
            if (!selectedScreen) return;
            const val = Math.max(0.5, parseFloat(e.target.value) || 1.0);
            selectedScreen.data.width = val;
            buildScreens();
            const reselected = screenObjects.find(so => so.data.id === selectedScreen.data.id);
            if (reselected) selectScreen(reselected);
            generate2DBlueprint();
        });

        document.getElementById('inspectScreenHeightInput')?.addEventListener('change', (e) => {
            if (!selectedScreen) return;
            const val = Math.max(0.5, parseFloat(e.target.value) || 1.0);
            selectedScreen.data.height = val;
            buildScreens();
            const reselected = screenObjects.find(so => so.data.id === selectedScreen.data.id);
            if (reselected) selectScreen(reselected);
            generate2DBlueprint();
        });

        // Panel CRUD: Delete Screen
        document.getElementById('btnDeleteScreen')?.addEventListener('click', () => {
            if (!selectedScreen) return;
            if (currentScreens.length <= 1) {
                alert('Panggung harus memiliki minimal 1 panel layar LED.');
                return;
            }
            const sId = selectedScreen.data.id;
            const idx = currentScreens.findIndex(sc => sc.id === sId);
            if (idx !== -1) {
                currentScreens.splice(idx, 1);
                selectedScreen = null;
                document.getElementById('screenInspectorBox').classList.add('hidden');
                buildScreens();
                generate2DBlueprint();
            }
        });

        // Panel CRUD: Add Custom Panel
        document.getElementById('btnAddCustomScreen')?.addEventListener('click', () => {
            const nextId = currentScreens.length > 0 ? (Math.max(...currentScreens.map(s => s.id)) + 1) : 1;
            const count = currentScreens.length + 1;
            const newPanel = {
                id: nextId,
                name: `Panel ${count}`,
                width: 3.0,
                height: 2.0,
                x: (count % 2 === 0 ? -1 : 1) * 1.5,
                z: -1.0,
                rotY: 0,
                rigging: "ground_stake"
            };
            currentScreens.push(newPanel);
            buildScreens();
            generate2DBlueprint();
            setTimeout(() => {
                const reselected = screenObjects.find(so => so.data.id === nextId);
                if (reselected) selectScreen(reselected);
            }, 50);
        });

        // Add Wing Button
        document.getElementById('btnAddWingScreen')?.addEventListener('click', () => {
            const count = currentScreens.length + 1;
            const isLeft = count % 2 === 0;
            const sideName = isLeft ? `Wing Kiri ${Math.floor(count/2)}` : `Wing Kanan ${Math.floor(count/2)}`;
            const posX = isLeft ? -(stageConfig.width / 2 - 1.5) : (stageConfig.width / 2 - 1.5);
            const rot = isLeft ? 20 : -20;

            currentScreens.push({
                id: count,
                name: sideName,
                width: 2.0,
                height: 3.0,
                x: posX,
                z: -1.0,
                rotY: rot,
                rigging: "ground_stake"
            });

            buildScreens();
            generate2DBlueprint();
        });

        // AJAX Save button (for orders/show)
        @if($saveUrl)
            document.getElementById('btnSaveStageLayoutAjax')?.addEventListener('click', function() {
                const btn = this;
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span>Menyimpan...</span>';

                generate2DBlueprint();

                fetch('{{ $saveUrl }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        stage_width_m: stageConfig.width,
                        stage_depth_m: stageConfig.depth,
                        stage_height_m: stageConfig.height,
                        stage_layout_json: currentScreens,
                        stage_layout_image: document.getElementById('inputStageLayoutImage')?.value,
                        stage_layout_front_image: document.getElementById('inputStageLayoutFrontImage')?.value
                    })
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    alert('✓ ' + (data.message || 'Layout panggung 3D & 2D blueprint berhasil disimpan!'));
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    alert('Gagal menyimpan layout: ' + err.message);
                });
            });
        @endif

        // Hook with Order Form screen dimension inputs (if on create page)
        const calcWidthInput = document.getElementById('calcWidth');
        const calcHeightInput = document.getElementById('calcHeight');
        if (calcWidthInput && calcHeightInput) {
            function updateMainScreenFromForm() {
                const w = parseFloat(calcWidthInput.value) || 6.0;
                const h = parseFloat(calcHeightInput.value) || 3.0;
                if (currentScreens.length > 0) {
                    currentScreens[0].width = w;
                    currentScreens[0].height = h;
                    buildScreens();
                    generate2DBlueprint();
                }
            }
            calcWidthInput.addEventListener('input', updateMainScreenFromForm);
            calcHeightInput.addEventListener('input', updateMainScreenFromForm);
        }
    }

    function onWindowResize() {
        if (!canvasDiv || !camera || !renderer) return;
        const width = canvasDiv.clientWidth;
        const height = canvasDiv.clientHeight;
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
    }

    function animate() {
        requestAnimationFrame(animate);
        if (controls) {
            controls.update();
        }
        if (renderer && scene && camera) {
            renderer.render(scene, camera);
        }
    }

    // Initialize when DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

