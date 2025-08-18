const tmdbApiKey = 'b492ccc669864b0f4df2aafe0485b8af';

const secretKey = 'oasis_secure_key_2025';

const categories = [

{ title: "Trending", icon: "fa-fire", type: "movie", movies: [

"To kill a Monkey", "Eyes of Wakanda", "Chief of War","M3GAN 2.0",

"Ballerina","The Old Guard 2","Karate Kid: Legends","The Fantastic 4","How to Train Your Dragon",

"Dangerous Animals","Superman","Thunderbolts","Lilo & Stitch","Dune: Part Two",

"Godzilla x Kong","Civil War","Deadpool & Wolverine","Inside Out 2","Straw",

"Diablo","Sinners","Final Destination: Bloodlines","Snow White","Fear Street: Prom Queen",

"A Minecraft Movie","Death of a Unicorn"

]},

{ title: "TV Shows", icon: "fa-tv", type: "tv", movies: [

"Severance","House of David","Loki","Breaking Bad","The Boys",

"Stranger Things","Sandman","When Life Gives You Tangerines","Doctor Who"

]},

{ title: "K-Drama", icon: "fa-heart", type: "tv", movies: [

"Crash Landing on You","Itaewon Class","Extraordinary Attorney Woo",

"Attack on Titan","The Lion King"

]},

{ title: "Cartoons", icon: "fa-child", type: "tv", movies: [

"The Simpsons","Eyes of Wakanda","common Side Effects","Wolf King",

"Wylde Pak","Goldie","The Mighty Neine","Harley Quinn","Rick and Morty"

]},

{ title: "Anime", icon: "fa-dragon", type: "tv", movies: [

"Sakamoto Days","Dandadan Season 2","Fire Force","Kaiju No. 8","Devil May Cry",

"Lazarus","Gachiakuta","Black Butler","Apocalyps Hotel","Duck Beyond the End of the World",

"spy x family","Bullet"

]}

];

const section = document.getElementById("movie-section");

categories.forEach((category, index) => {

const catDiv = document.createElement("div");

catDiv.className = "category";

const title = document.createElement("h2");

title.innerHTML = `<span class="icon-title"><i class="fas ${category.icon}"></i> ${category.title}</span>`;

catDiv.appendChild(title);

const wrapper = document.createElement("div");

wrapper.className = "movie-wrapper";

const leftBtn = document.createElement("button");

leftBtn.className = "scroll-btn left";

leftBtn.innerHTML = `<i class="fa-solid fa-less-than"></i>`;

wrapper.appendChild(leftBtn);

const row = document.createElement("div");

row.className = "movie-row";

row.id = `row-${index}`;

wrapper.appendChild(row);

const rightBtn = document.createElement("button");

rightBtn.className = "scroll-btn right";

rightBtn.innerHTML = `<i class="fa-solid fa-greater-than"></i>`;

wrapper.appendChild(rightBtn);

catDiv.appendChild(wrapper);

section.appendChild(catDiv);

const loading = document.createElement("div");

loading.className = "loading";

loading.innerText = "Loading...";

row.appendChild(loading);

Promise.all(

category.movies.map(movieName =>

fetch(`https://api.themoviedb.org/3/search/${category.type}?api_key=${tmdbApiKey}&query=${encodeURIComponent(movieName)}`)

.then(res => res.json())

.then(data => {

if (!data.results || data.results.length === 0) return;

const validResults = data.results.filter(m => m.poster_path);

if (validResults.length === 0) return;

const sortedResults = validResults.sort((a, b) => b.popularity - a.popularity);

const result = sortedResults[0];

const year = result.release_date || result.first_air_date

? new Date(result.release_date || result.first_air_date).getFullYear()

: "";

const fullMovieName = year

? `${result.title || result.name} (${year})`

: (result.title || result.name);

const imgSrc = `https://image.tmdb.org/t/p/w500${result.poster_path}`;

const movieEl = document.createElement("div");

movieEl.className = "movie";

movieEl.innerHTML = `

<img src="${imgSrc}" alt="${fullMovieName}">

<button class="download-btn">Download</button>

`;

// --- ✅ Simplified Download (works everywhere, mobile + XAMPP) ---

const triggerDownload = () => {

const ts = Math.floor(Date.now() / 1000);

const token = btoa(fullMovieName + ts); // base64 simple token

const url = `download.php?movie=${encodeURIComponent(fullMovieName)}&token=${token}&ts=${ts}`;

window.location.href = url;

};

movieEl.querySelector(".download-btn").addEventListener("click", triggerDownload);

// --- Make poster image also clickable ---

const posterImg = movieEl.querySelector("img");

if (posterImg) {

posterImg.style.cursor = 'pointer';

posterImg.addEventListener('click', triggerDownload);

}

row.appendChild(movieEl);

})

)

).finally(() => {

row.removeChild(loading);

});

// Auto-scroll

setInterval(() => {

row.scrollBy({ left: 160, behavior: "smooth" });

if (row.scrollLeft + row.clientWidth >= row.scrollWidth) {

row.scrollTo({ left: 0, behavior: "smooth" });

}

}, 4000 + index * 1000);

// Manual scroll

leftBtn.addEventListener("click", () => {

row.scrollBy({ left: -300, behavior: "smooth" });

});

rightBtn.addEventListener("click", () => {

row.scrollBy({ left: 300, behavior: "smooth" });

});

});
