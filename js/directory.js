async function saveDirectoryHandle(dirHandle) {
    const db = await idb.openDB("directoryStorage", 1, {
        upgrade(db) {
            db.createObjectStore("handles");
        },
    });

    await db.put("handles", dirHandle, "userDirectory");
}

async function getDirectoryHandle() {
    const db = await idb.openDB("directoryStorage", 1);
    return await db.get("handles", "userDirectory");
}

async function useSavedDirectory() {
    const dirHandle = await getDirectoryHandle();

    if (dirHandle) {
        const permission = await dirHandle.requestPermission({ mode: "readwrite" });

        if (permission === "granted") {
            console.log("Access to saved directory handle granted:", dirHandle);
            // Use the directory handle here
            document.getElementById("directoryName").value = dirHandle.name;
        } else {
            console.error("Permission denied to access saved directory handle.");
        }
    } else {
        console.error("No saved directory handle found.");
    }
}

async function readDirectoryFiles() {
    const dirHandle = await getDirectoryHandle();

    if (dirHandle) {
        const permission = await dirHandle.requestPermission({ mode: "read" });

        if (permission === "granted") {
            console.log("Access to saved directory handle granted:", dirHandle);
            for await (const entry of dirHandle.values()) {
                console.log(entry.name);
            }
            console.log("Done reading directory files.");

        } else {
            console.error("Permission denied to access saved directory handle.");
        }
    } else {
        console.error("No saved directory handle found.");
    }
}

async function saveKeyImageToDirectory(file) {
    const dirHandle = await getDirectoryHandle();

    if (dirHandle) {
        const subDirHandle = await dirHandle.getDirectoryHandle("KEYS", {
            create: true,
        });
        if (subDirHandle) {
            const fileHandle = await subDirHandle.getFileHandle(file.name, { create: true });

            const writable = await fileHandle.createWritable();
            await writable.write(file);
            await writable.close();
        } else {
            console.error("No saved sub directory handle found.");
        }
    } else {
        console.error("No saved directory handle found.");
    }
}

async function saveCipherImageToDirectory(file) {
    const dirHandle = await getDirectoryHandle();

    if (dirHandle) {
        const subDirHandle = await dirHandle.getDirectoryHandle("CIPHER_TEXT", {
            create: true,
        });
        if (subDirHandle) {
            const fileHandle = await subDirHandle.getFileHandle(file.name, { create: true });

            const writable = await fileHandle.createWritable();
            await writable.write(file);
            await writable.close();
        } else {
            console.error("No saved sub directory handle found.");
        }
    } else {
        console.error("No saved directory handle found.");
    }
}