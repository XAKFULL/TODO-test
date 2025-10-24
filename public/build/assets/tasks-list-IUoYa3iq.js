document.addEventListener("DOMContentLoaded",()=>{window.loadTasks(1),window.loadAllTags(),window.initTagsInput()});document.addEventListener("change",e=>{e.target.id==="sort-by"&&window.loadTasks(1)});let r=[],c=[];function u(e=""){const n=document.getElementById("tag-suggestions");if(!n)return;const t=e.toLowerCase(),d=r.filter(s=>!c.includes(s));let o=d;t&&(o=d.filter(s=>s.toLowerCase().includes(t))),o=o.slice(0,10),o.length>0?(n.innerHTML="",o.forEach(s=>{const a=document.createElement("div");a.className="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm",a.textContent=s,a.addEventListener("click",()=>{p(s),document.getElementById("tag-input").value="",n.classList.add("hidden"),document.getElementById("tag-input").focus()}),n.appendChild(a)}),n.classList.remove("hidden")):n.classList.add("hidden")}window.initTagsInput=function(){const e=document.getElementById("tag-input"),n=document.getElementById("tag-suggestions");document.getElementById("selected-tags"),!(!e||!n)&&(e.addEventListener("focus",()=>{u("")}),e.addEventListener("input",()=>{const t=e.value.trim().toLowerCase();u(t)}),e.addEventListener("blur",()=>{setTimeout(()=>{n.classList.add("hidden")},150)}),document.addEventListener("click",t=>{!t.target.closest("#tag-input")&&!t.target.closest("#tag-suggestions")&&n.classList.add("hidden")}))};function p(e){c.includes(e)||(c.push(e),g(),typeof window.loadTasks=="function"&&window.loadTasks(1))}window.removeTag=function(e){c=c.filter(n=>n!==e),g(),typeof window.loadTasks=="function"&&window.loadTasks(1)};function g(){const e=document.getElementById("selected-tags");e&&(e.innerHTML="",c.forEach(n=>{const t=document.createElement("span");t.className="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded flex items-center",t.innerHTML=`
            ${n}
            <button type="button" class="ml-1 text-blue-600 hover:text-blue-900" onclick="removeTag('${n.replace(/'/g,"\\'")}')">
                &times;
            </button>
        `,e.appendChild(t)}))}window.loadAllTags=function(){fetch("/api/tags").then(e=>e.json()).then(e=>{r=e.data.map(t=>t.name),console.log("Все теги:",r)}).catch(e=>console.error("Ошибка загрузки тегов:",e))};window.loadTasks=function(e=1){const n=document.getElementById("search-input").value,t=document.getElementById("status-filter").value,d=document.getElementById("per-page").value,o=document.getElementById("sort-by")?.value||"created_at|desc",[s,a]=o.split("|"),l={page:e,per_page:d,search:n||void 0,status:t||void 0,...c.length>0&&{tags:c},sort:s,direction:a,with_tags:!0};Object.keys(l).forEach(i=>l[i]===void 0&&delete l[i]),fetch("/api/tasks-paginated",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),Accept:"application/json"},body:JSON.stringify(l)}).then(i=>{if(!i.ok)throw new Error("Ошибка загрузки");return i.json()}).then(i=>{renderTasks(i.data),renderPagination(i)}).catch(i=>{console.error(i),document.getElementById("tasks-table-body").innerHTML='<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Ошибка загрузки</td></tr>'})};window.renderTasks=function(e){const n=document.getElementById("tasks-table-body");if(n.innerHTML="",e.length===0){n.innerHTML='<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Задач не найдено</td></tr>';return}e.forEach(t=>{const d={pending:"Ожидает",in_progress:"В работе",completed:"Завершено"},o=t.tags?.map(a=>`<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">${a}</span>`).join("")||'<span class="text-gray-500">—</span>',s=`
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="font-medium">${t.id}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-medium">${t.title}</div>
                    <div class="text-sm text-gray-500 mt-1">${t.description||""}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-xs ${t.status_code==="completed"?"bg-green-100 text-green-800":t.status_code==="in_progress"?"bg-yellow-100 text-yellow-800":"bg-gray-100 text-gray-800"}">
                        ${d[t.status_code]||t.status_code}
                    </span>
                </td>
                <td class="px-6 py-4">${o}</td>
                <td class="px-6 py-4">
                    <button type="button" class="text-blue-500 text-sm mr-2"
                            onclick="openTaskDetail(${t.id})">
                        Открыть
                    </button>
                    <button type="button" class="text-blue-500 text-sm mr-2"
                            onclick="openEditModal(${t.id})">
                        Редактировать
                    </button>
                    <button type="button" class="text-red-500 text-sm"
                            onclick="openDeleteModal(${t.id}, ${t.title})">
                        Удалить
                    </button>
                </td>
            </tr>
        `;n.innerHTML+=s})};window.renderPagination=function(e){const n=document.getElementById("pagination"),{current_page:t,last_page:d,total:o}=e.meta;let s=`<div class="text-sm text-gray-600">Всего: ${o}</div>`;if(d>1){const a=t===1?"disabled":"",l=t===d?"disabled":"";s+=`
            <div class="flex space-x-2">
                <button ${a} onclick="loadTasks(${t-1})"
                        class="px-3 py-1 border rounded ${a?"text-gray-400":"hover:bg-gray-100"}">
                    Назад
                </button>
                <span class="px-3 py-1">Стр. ${t} из ${d}</span>
                <button ${l} onclick="loadTasks(${t+1})"
                        class="px-3 py-1 border rounded ${l?"text-gray-400":"hover:bg-gray-100"}">
                    Вперёд
                </button>
            </div>
        `}n.innerHTML=s};document.getElementById("search-input")?.addEventListener("input",()=>loadTasks());document.getElementById("status-filter")?.addEventListener("change",()=>loadTasks());document.getElementById("tag-filter")?.addEventListener("change",()=>loadTasks());document.getElementById("per-page")?.addEventListener("change",()=>loadTasks());
