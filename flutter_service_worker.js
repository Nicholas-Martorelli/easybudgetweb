'use strict';
const MANIFEST = 'flutter-app-manifest';
const TEMP = 'flutter-temp-cache';
const CACHE_NAME = 'flutter-app-cache';

const RESOURCES = {".git/COMMIT_EDITMSG": "e05864e1fd9b5686a8897817a312c4c3",
".git/config": "7a8ab72abf7ff15148eff1904f4696cc",
".git/description": "a0a7c3fff21f2aea3cfa1d0316dd816c",
".git/HEAD": "5ab7a4355e4c959b0c5c008f202f51ec",
".git/hooks/applypatch-msg.sample": "ce562e08d8098926a3862fc6e7905199",
".git/hooks/commit-msg.sample": "579a3c1e12a1e74a98169175fb913012",
".git/hooks/fsmonitor-watchman.sample": "ea587b0fae70333bce92257152996e70",
".git/hooks/post-update.sample": "2b7ea5cee3c49ff53d41e00785eb974c",
".git/hooks/pre-applypatch.sample": "054f9ffb8bfe04a599751cc757226dda",
".git/hooks/pre-commit.sample": "305eadbbcd6f6d2567e033ad12aabbc4",
".git/hooks/pre-merge-commit.sample": "39cb268e2a85d436b9eb6f47614c3cbc",
".git/hooks/pre-push.sample": "2c642152299a94e05ea26eae11993b13",
".git/hooks/pre-rebase.sample": "56e45f2bcbc8226d2b4200f7c46371bf",
".git/hooks/pre-receive.sample": "2ad18ec82c20af7b5926ed9cea6aeedd",
".git/hooks/prepare-commit-msg.sample": "2b5c047bdb474555e1787db32b2d2fc5",
".git/hooks/push-to-checkout.sample": "c7ab00c7784efeadad3ae9b228d4b4db",
".git/hooks/update.sample": "647ae13c682f7827c22f5fc08a03674e",
".git/index": "b488027fb2d41e399c889a6d673d5d46",
".git/info/exclude": "036208b4a1ab4a235d75c181e685e5a3",
".git/logs/HEAD": "32670d62263a3035c8a17d7730f47a27",
".git/logs/refs/heads/gh-pages": "32670d62263a3035c8a17d7730f47a27",
".git/logs/refs/remotes/origin/gh-pages": "f347dc4cf62efcebf4b9327462b96b1c",
".git/objects/08/e833b889481b6d2a7dd1ddf0fd430fa80fc7eb": "dcd770a2a7c223c581a1abf8e86c7684",
".git/objects/13/0349e44662a682d5f639d42f7255319048cd5d": "bd9987013a39a1385b755b54c6d9e935",
".git/objects/1a/d7683b343914430a62157ebf451b9b2aa95cac": "94fdc36a022769ae6a8c6c98e87b3452",
".git/objects/1f/1fbd81f36196e3c175e4c2e48a4f4b3976ed5a": "293f12b7f8b4aaccbd11a6018dd790e3",
".git/objects/28/0e9c4b475cd9a027af95d3ce1e2d8200679fa8": "48b7a914b91cc53b38fe832774cb4af9",
".git/objects/2a/dcc7e9656e48d5e29fb16e7376d0559afb4455": "c404619dab007be933245fed6de4340a",
".git/objects/30/c2d871461d6ef7f25c6b62bc94566e80ee69ad": "5dcf4f210fc1ff5ce928d59ba177d6dd",
".git/objects/35/2695e9a482608635d45b68feeccebfd7826a3b": "321bde6526c0052fdaf7c15cc33c7184",
".git/objects/35/400db8d85fe2ae71584931a0cebe5634913849": "f2c91922004fc38da3cee50fca668d41",
".git/objects/41/37511e39dd884587774cfc0467c1f0e9dc8bcd": "934730cd9496305b0310c88334a8ca28",
".git/objects/45/e9093cd5f9cdcf1a131f7661f1370667d80aea": "1f3bf18ed1300cabb87f4d8c1917abbe",
".git/objects/47/754a2bd403ed75e6d5be456559d13d7540c36e": "012a3215ddba0e41a8d6d7bf973316a3",
".git/objects/4b/e3c781219a5d0030da94f593656e6c2238f975": "f1bd5e55f17801f06d394abccd9dcdc4",
".git/objects/4c/51fb2d35630595c50f37c2bf5e1ceaf14c1a1e": "a20985c22880b353a0e347c2c6382997",
".git/objects/53/18a6956a86af56edbf5d2c8fdd654bcc943e88": "a686c83ba0910f09872b90fd86a98a8f",
".git/objects/53/3d2508cc1abb665366c7c8368963561d8c24e0": "4592c949830452e9c2bb87f305940304",
".git/objects/55/b89a0f934e2ecd79dbd565b65022da358e706d": "8881858cf34ca023ee2310d61807ea44",
".git/objects/5e/e8438677ae4be1834dc4d5fd415b63a61a6d46": "15bcf2ed0bbd1e4582c8f3431a44e709",
".git/objects/64/8891eb1354c7376f97aa6ce73683ae95782f0f": "4085cddd247c83d43ec3cb237ed0151a",
".git/objects/68/4ab17fb574bcd09a317430a550b64c9c2ffc0f": "88a8c25a0fc5932d3af42db1a1966ed6",
".git/objects/6b/9862a1351012dc0f337c9ee5067ed3dbfbb439": "85896cd5fba127825eb58df13dfac82b",
".git/objects/70/a234a3df0f8c93b4c4742536b997bf04980585": "d95736cd43d2676a49e58b0ee61c1fb9",
".git/objects/73/6dea7334d84a80befc84578dedb8d274e23d7b": "ecd46e1a500f60703c81ec7eaef813da",
".git/objects/73/c63bcf89a317ff882ba74ecb132b01c374a66f": "6ae390f0843274091d1e2838d9399c51",
".git/objects/73/fe541f5aa4224b3f98bfb6507bda3acfc94a15": "87d05cdb5b6448c902c9bc89ef604b43",
".git/objects/7b/6dd265468ff9e7fb6f75a8d53e4fb0bce7c30b": "5a006dacf09bbae885ac080554c24a6e",
".git/objects/7e/2a811b94aee55f7f60958b414ca287e7e25c7d": "71fca5fd4291db99c8093bfd7af54a77",
".git/objects/88/cfd48dff1169879ba46840804b412fe02fefd6": "e42aaae6a4cbfbc9f6326f1fa9e3380c",
".git/objects/8a/aa46ac1ae21512746f852a42ba87e4165dfdd1": "1d8820d345e38b30de033aa4b5a23e7b",
".git/objects/8d/b4984ef99d615e64f244811b3211965ad4abad": "31de28dc763a47c2bd8b603b38304cf0",
".git/objects/8e/3c7d6bbbef6e7cefcdd4df877e7ed0ee4af46e": "025a3d8b84f839de674cd3567fdb7b1b",
".git/objects/8e/8f79a2195af54327d70df1f3294ebf5d1d21c4": "f83e6f52297754ba7bb42d60dc74dfca",
".git/objects/91/938b911e425853def59973d717734a0ef948ba": "c3d2f0aacb47d43603bfcfcb7f89ea15",
".git/objects/91/d208ca821d68fefcd4ebc685b695603669e385": "1812c5f5f9c52bdc36143691ab5fbc5a",
".git/objects/97/d5aa523c826e85b98d64ee14e3ebcefa378563": "827798b84f9cfe690e6a7208966d03d1",
".git/objects/9b/d3accc7e6a1485f4b1ddfbeeaae04e67e121d8": "784f8e1966649133f308f05f2d98214f",
".git/objects/9c/106e770721ca273311b0fed64f3bb5111c8929": "ea8cb910330d8a18a40f7890629274c0",
".git/objects/9c/22d5a69dfbff5cda59aaee3663d31cb32bd50c": "6b1ec5baeccb2f0a80819e5b255d6b7b",
".git/objects/b1/44f7ca728d4c29b8ab7942804a9d552c7e4203": "c06e33c505e114e80f32711fbb07f327",
".git/objects/b4/3f2ca3deb26ee6eee23919caf9c4a0b46d2d8a": "89f7ccce9b05902a0083c32194626a80",
".git/objects/b5/89a335b84f1583b40374caa4e09ad0baa8d6ef": "74a97f03df847d7d4cf0ae50d5e067f3",
".git/objects/b7/49bfef07473333cf1dd31e9eed89862a5d52aa": "36b4020dca303986cad10924774fb5dc",
".git/objects/b9/2a0d854da9a8f73216c4a0ef07a0f0a44e4373": "f62d1eb7f51165e2a6d2ef1921f976f3",
".git/objects/b9/6a5236065a6c0fb7193cb2bb2f538b2d7b4788": "4227e5e94459652d40710ef438055fe5",
".git/objects/bb/abc76ddd9124de60b45e4fcbe7baebdf79eef2": "e1ebdc83210c89b6bcaa91ee5fd08161",
".git/objects/bc/82e7e58258abac4c442854a5a524d17d607b0b": "4f630249cfbb184eed8b885639b9110d",
".git/objects/c3/1fd0b25f5c8111443b008bdb344a4b8495b4f9": "6a5802fd93c23b446375be0361b55c08",
".git/objects/c4/ebad28239876de1b986a2427376b077c1a0220": "c7098e473a1fff4a06cf5b48046ed03b",
".git/objects/c5/56b752dab5edb427817d0a727a190c9d81ee9a": "16ba00076763091c260cedde6a18b020",
".git/objects/c8/08fb85f7e1f0bf2055866aed144791a1409207": "92cdd8b3553e66b1f3185e40eb77684e",
".git/objects/c8/3e188bf5524dcc625c88381473c358999681d3": "345a3414a96c673803baf76803eb88bd",
".git/objects/cc/09c7f5fcf313453eca24f8ceec4f8fbfc1d225": "9461d3028da79a0612a5279bb9791892",
".git/objects/d4/3532a2348cc9c26053ddb5802f0e5d4b8abc05": "3dad9b209346b1723bb2cc68e7e42a44",
".git/objects/d6/9c56691fbdb0b7efa65097c7cc1edac12a6d3e": "868ce37a3a78b0606713733248a2f579",
".git/objects/dc/11fdb45a686de35a7f8c24f3ac5f134761b8a9": "761c08dfe3c67fe7f31a98f6e2be3c9c",
".git/objects/df/14c6728d5eac03526dbab6f17f5eae3ad875bf": "1429849fbf3666ce78603dfd8708cce3",
".git/objects/e0/7ac7b837115a3d31ed52874a73bd277791e6bf": "74ebcb23eb10724ed101c9ff99cfa39f",
".git/objects/e5/a963dd614709cdb92173d4065dad097a34ee39": "53319f383b0dc1175358f694c37ac7c0",
".git/objects/e9/94225c71c957162e2dcc06abe8295e482f93a2": "2eed33506ed70a5848a0b06f5b754f2c",
".git/objects/ea/b07d8295265f6cec623f8352cf5de13850fb42": "8d52329202fe8566d02b110737ee0d6f",
".git/objects/eb/9b4d76e525556d5d89141648c724331630325d": "37c0954235cbe27c4d93e74fe9a578ef",
".git/objects/ee/e0fd19828fffcace293cca8064ff03fa4a2d91": "67738ecebb4a7a9c6ba5b2db8efe6cf0",
".git/objects/f0/0f017c6a549a43fbb023330a490aecb1a3da72": "eacd26f7a4b65e02512b76db1c106c48",
".git/objects/f2/04823a42f2d890f945f70d88b8e2d921c6ae26": "6b47f314ffc35cf6a1ced3208ecc857d",
".git/objects/f2/88896e9fc06e46a5fbe9453da5495e84168d0e": "6efedd3917e8cdbd66d553354b69271e",
".git/objects/f5/72b90ef57ee79b82dd846c6871359a7cb10404": "e68f5265f0bb82d792ff536dcb99d803",
".git/objects/f8/aae3fa8c95f5da981026bb6f99b5ba45c2258c": "3a0b12100f2d086de262033a2611185f",
".git/objects/fa/4c3136821079e1877afd859dbbaa9ba5ed0689": "f3ec2e970999774cf78a203a0184e254",
".git/objects/fd/57646f9c347e235078efbad692f5e0c42206e8": "dad3257be4c862afaa38698ec7d5c5e7",
".git/objects/fe/ef61c36129025a39902f9f3f923dbd22239921": "d4b35ad2228773a043e4b7d644a5f6c3",
".git/refs/heads/gh-pages": "ec6c5ed1ea14852093bbbe1df70657d1",
".git/refs/remotes/origin/gh-pages": "ec6c5ed1ea14852093bbbe1df70657d1",
"assets/AssetManifest.bin": "c70cf5c810235b7622a7b2faecf09709",
"assets/AssetManifest.bin.json": "8afe33ae86b37f1a873941b68232dd51",
"assets/AssetManifest.json": "db36e87e15dd3d43c04911ea6d609785",
"assets/FontManifest.json": "1ee00d31df7d0b30bfafc1cf4922abf8",
"assets/fonts/MaterialIcons-Regular.otf": "5b785c494e71702169f93b9c7a2d3eef",
"assets/NOTICES": "572aa6cd00ff4a3a94dcae75a1d2c53c",
"assets/packages/cupertino_icons/assets/CupertinoIcons.ttf": "33b7d9392238c04c131b6ce224e13711",
"assets/packages/lucide_icons/assets/lucide.ttf": "03f254a55085ec6fe9a7ae1861fda9fd",
"assets/shaders/ink_sparkle.frag": "ecc85a2e95f5e9f53123dcaf8cb9b6ce",
"canvaskit/canvaskit.js": "728b2d477d9b8c14593d4f9b82b484f3",
"canvaskit/canvaskit.js.symbols": "bdcd3835edf8586b6d6edfce8749fb77",
"canvaskit/canvaskit.wasm": "7a3f4ae7d65fc1de6a6e7ddd3224bc93",
"canvaskit/chromium/canvaskit.js": "8191e843020c832c9cf8852a4b909d4c",
"canvaskit/chromium/canvaskit.js.symbols": "b61b5f4673c9698029fa0a746a9ad581",
"canvaskit/chromium/canvaskit.wasm": "f504de372e31c8031018a9ec0a9ef5f0",
"canvaskit/skwasm.js": "ea559890a088fe28b4ddf70e17e60052",
"canvaskit/skwasm.js.symbols": "e72c79950c8a8483d826a7f0560573a1",
"canvaskit/skwasm.wasm": "39dd80367a4e71582d234948adc521c0",
"favicon.png": "5dcef449791fa27946b3d35ad8803796",
"flutter.js": "83d881c1dbb6d6bcd6b42e274605b69c",
"flutter_bootstrap.js": "d181c5adfb3a8ceeebf3701607474763",
"icons/Icon-192.png": "ac9a721a12bbc803b44f645561ecb1e1",
"icons/Icon-512.png": "96e752610906ba2a93c65f8abe1645f1",
"icons/Icon-maskable-192.png": "c457ef57daa1d16f64b27b786ec2ea3c",
"icons/Icon-maskable-512.png": "301a7604d45b3e739efc881eb04896ea",
"index.html": "2c45e8470adc783384a01a26ea3c75cb",
"/": "2c45e8470adc783384a01a26ea3c75cb",
"main.dart.js": "3115174ad4161463277432a910a52bef",
"manifest.json": "3958f303f0e5143996b212057a76c65e",
"version.json": "010f4a73412bf6710c9ec53a276a5c58"};
// The application shell files that are downloaded before a service worker can
// start.
const CORE = ["main.dart.js",
"index.html",
"flutter_bootstrap.js",
"assets/AssetManifest.bin.json",
"assets/FontManifest.json"];

// During install, the TEMP cache is populated with the application shell files.
self.addEventListener("install", (event) => {
  self.skipWaiting();
  return event.waitUntil(
    caches.open(TEMP).then((cache) => {
      return cache.addAll(
        CORE.map((value) => new Request(value, {'cache': 'reload'})));
    })
  );
});
// During activate, the cache is populated with the temp files downloaded in
// install. If this service worker is upgrading from one with a saved
// MANIFEST, then use this to retain unchanged resource files.
self.addEventListener("activate", function(event) {
  return event.waitUntil(async function() {
    try {
      var contentCache = await caches.open(CACHE_NAME);
      var tempCache = await caches.open(TEMP);
      var manifestCache = await caches.open(MANIFEST);
      var manifest = await manifestCache.match('manifest');
      // When there is no prior manifest, clear the entire cache.
      if (!manifest) {
        await caches.delete(CACHE_NAME);
        contentCache = await caches.open(CACHE_NAME);
        for (var request of await tempCache.keys()) {
          var response = await tempCache.match(request);
          await contentCache.put(request, response);
        }
        await caches.delete(TEMP);
        // Save the manifest to make future upgrades efficient.
        await manifestCache.put('manifest', new Response(JSON.stringify(RESOURCES)));
        // Claim client to enable caching on first launch
        self.clients.claim();
        return;
      }
      var oldManifest = await manifest.json();
      var origin = self.location.origin;
      for (var request of await contentCache.keys()) {
        var key = request.url.substring(origin.length + 1);
        if (key == "") {
          key = "/";
        }
        // If a resource from the old manifest is not in the new cache, or if
        // the MD5 sum has changed, delete it. Otherwise the resource is left
        // in the cache and can be reused by the new service worker.
        if (!RESOURCES[key] || RESOURCES[key] != oldManifest[key]) {
          await contentCache.delete(request);
        }
      }
      // Populate the cache with the app shell TEMP files, potentially overwriting
      // cache files preserved above.
      for (var request of await tempCache.keys()) {
        var response = await tempCache.match(request);
        await contentCache.put(request, response);
      }
      await caches.delete(TEMP);
      // Save the manifest to make future upgrades efficient.
      await manifestCache.put('manifest', new Response(JSON.stringify(RESOURCES)));
      // Claim client to enable caching on first launch
      self.clients.claim();
      return;
    } catch (err) {
      // On an unhandled exception the state of the cache cannot be guaranteed.
      console.error('Failed to upgrade service worker: ' + err);
      await caches.delete(CACHE_NAME);
      await caches.delete(TEMP);
      await caches.delete(MANIFEST);
    }
  }());
});
// The fetch handler redirects requests for RESOURCE files to the service
// worker cache.
self.addEventListener("fetch", (event) => {
  if (event.request.method !== 'GET') {
    return;
  }
  var origin = self.location.origin;
  var key = event.request.url.substring(origin.length + 1);
  // Redirect URLs to the index.html
  if (key.indexOf('?v=') != -1) {
    key = key.split('?v=')[0];
  }
  if (event.request.url == origin || event.request.url.startsWith(origin + '/#') || key == '') {
    key = '/';
  }
  // If the URL is not the RESOURCE list then return to signal that the
  // browser should take over.
  if (!RESOURCES[key]) {
    return;
  }
  // If the URL is the index.html, perform an online-first request.
  if (key == '/') {
    return onlineFirst(event);
  }
  event.respondWith(caches.open(CACHE_NAME)
    .then((cache) =>  {
      return cache.match(event.request).then((response) => {
        // Either respond with the cached resource, or perform a fetch and
        // lazily populate the cache only if the resource was successfully fetched.
        return response || fetch(event.request).then((response) => {
          if (response && Boolean(response.ok)) {
            cache.put(event.request, response.clone());
          }
          return response;
        });
      })
    })
  );
});
self.addEventListener('message', (event) => {
  // SkipWaiting can be used to immediately activate a waiting service worker.
  // This will also require a page refresh triggered by the main worker.
  if (event.data === 'skipWaiting') {
    self.skipWaiting();
    return;
  }
  if (event.data === 'downloadOffline') {
    downloadOffline();
    return;
  }
});
// Download offline will check the RESOURCES for all files not in the cache
// and populate them.
async function downloadOffline() {
  var resources = [];
  var contentCache = await caches.open(CACHE_NAME);
  var currentContent = {};
  for (var request of await contentCache.keys()) {
    var key = request.url.substring(origin.length + 1);
    if (key == "") {
      key = "/";
    }
    currentContent[key] = true;
  }
  for (var resourceKey of Object.keys(RESOURCES)) {
    if (!currentContent[resourceKey]) {
      resources.push(resourceKey);
    }
  }
  return contentCache.addAll(resources);
}
// Attempt to download the resource online before falling back to
// the offline cache.
function onlineFirst(event) {
  return event.respondWith(
    fetch(event.request).then((response) => {
      return caches.open(CACHE_NAME).then((cache) => {
        cache.put(event.request, response.clone());
        return response;
      });
    }).catch((error) => {
      return caches.open(CACHE_NAME).then((cache) => {
        return cache.match(event.request).then((response) => {
          if (response != null) {
            return response;
          }
          throw error;
        });
      });
    })
  );
}
