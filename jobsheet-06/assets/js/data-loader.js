async function loadData(fileName, keys) {
    const response = await fetch("../data/" + fileName);

    if (!response.ok) {
        throw new Error("Failed to fetch data (status " + response.status + ")");
    }

    const data = await response.json();

    return data.map(function (item) {
        const result = {};

        keys.forEach(function (key) {
            result[key] = item[key];
        });

        return result;
    });
}