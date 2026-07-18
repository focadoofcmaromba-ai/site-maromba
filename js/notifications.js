/*
====================================================
 DYXON MAROMBA
 Sistema de Notificações
====================================================
*/

"use strict";

const STORAGE_KEY = "dyxon_notifications";

let notifications = [];

let notificationRoot = null;
let notificationBtn = null;
let notificationBadge = null;
let notificationPanel = null;
let notificationList = null;

function waitForNavbar() {

    const interval = setInterval(() => {

        notificationRoot = document.getElementById("notificationRoot");

        if (notificationRoot) {

            clearInterval(interval);

            buildNotificationUI();

            loadNotifications();

        }

    }, 100);

}

function buildNotificationUI() {

    notificationRoot.innerHTML = `

        <button
            id="notificationBtn"
            class="notification-btn"
            aria-label="Notificações">

            🔔

            <span
                id="notificationBadge"
                class="notification-badge">

                0

            </span>

        </button>

        <div
            id="notificationPanel"
            class="notification-panel">

            <div class="notification-header">

                Novidades

            </div>

            <div
                id="notificationList"
                class="notification-list">

                <div class="notification-empty">

                    Carregando...

                </div>

            </div>

        </div>

    `;

    notificationBtn =
        document.getElementById("notificationBtn");

    notificationBadge =
        document.getElementById("notificationBadge");

    notificationPanel =
        document.getElementById("notificationPanel");

    notificationList =
        document.getElementById("notificationList");

    notificationBtn.addEventListener(
        "click",
        toggleNotificationPanel
    );

    document.addEventListener(
        "click",
        closeOutside
    );

    document.addEventListener(
        "keydown",
        closeEsc
    );

}

async function loadNotifications() {

    try {

        const response = await fetch(
            "data/notifications.json",
            {
                cache: "no-store"
            }
        );

        if (!response.ok) {

            throw new Error(
                "Erro ao carregar notifications.json"
            );

        }

        notifications = await response.json();

        notifications.sort((a, b) => {

            return new Date(b.data) - new Date(a.data);

        });

        renderNotifications();

    } catch (error) {

        console.error(error);

        notificationList.innerHTML = `

            <div class="notification-empty">

                Não foi possível carregar
                as notificações.

            </div>

        `;

    }

}

function getReadNotifications() {

    return JSON.parse(

        localStorage.getItem(STORAGE_KEY) || "{}"

    );

}

function saveReadNotifications(data) {

    localStorage.setItem(

        STORAGE_KEY,

        JSON.stringify(data)

    );

}

function renderNotifications() {

    const read = getReadNotifications();

    const unread = notifications.filter(item => {

        return read[item.id] !== item.version;

    });

    notificationBadge.textContent = unread.length;

    notificationBadge.style.display =

        unread.length > 0

            ? "flex"

            : "none";

    notificationList.innerHTML = "";

    if (unread.length === 0) {

        notificationList.innerHTML = `

            <div class="notification-empty">

                Nenhuma novidade.

            </div>

        `;

        return;

    }

    unread.forEach(createNotificationItem);

}

function createNotificationItem(notification) {

    const item = document.createElement("div");

    item.className = "notification-item";

    item.innerHTML = `

        <div class="notification-item-header">

            <span class="notification-type">

                ${notification.tipo}

            </span>

            <span class="notification-date">

                ${formatDate(notification.data)}

            </span>

        </div>

        <div class="notification-item-title">

            ${notification.titulo}

        </div>

        <div class="notification-item-description">

            ${notification.descricao}

        </div>

    `;

    item.addEventListener("click", () => {

        console.log("CLIQUE", notification);

        markAsRead(notification);

    });

    notificationList.appendChild(item);

}

function markAsRead(notification) {

    const read = getReadNotifications();

    read[notification.id] = notification.version;

    saveReadNotifications(read);

    renderNotifications();

    closeNotificationPanel();

    if (

        notification.link &&
        notification.link !== "#"

    ) {

        window.location.href = notification.link;

    }

}

function formatDate(date) {

    return new Date(date).toLocaleDateString(

        "pt-BR",

        {

            day: "2-digit",

            month: "2-digit",

            year: "numeric"

        }

    );

}

function toggleNotificationPanel(event) {

    if (event) {

        event.stopPropagation();

    }

    notificationPanel.classList.toggle("active");

}

function closeNotificationPanel() {

    notificationPanel.classList.remove("active");

}

function closeOutside(event) {

    if (

        !notificationPanel ||

        !notificationBtn

    ) {

        return;

    }

    if (

        notificationPanel.contains(event.target)

    ) {

        return;

    }

    if (

        notificationBtn.contains(event.target)

    ) {

        return;

    }

    closeNotificationPanel();

}

function closeEsc(event) {

    if (

        event.key === "Escape"

    ) {

        closeNotificationPanel();

    }

}

function refreshNotifications() {

    loadNotifications();

}

window.addEventListener(

    "focus",

    refreshNotifications

);

/*
====================================================
 Inicialização
====================================================
*/

function initNotifications() {

    waitForNavbar();

}

if (

    document.readyState === "loading"

) {

    document.addEventListener(

        "DOMContentLoaded",

        initNotifications

    );

} else {

    initNotifications();

}