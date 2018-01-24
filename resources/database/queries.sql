CREATE TABLE 'session' ('id' TEXT PRIMARY KEY NOT NULL, 'uid' TEXT NOT NULL, 'secret' TEXT NOT NULL, 'name' TEXT DEFAULT 'Session Name', 'created_at' DATETIME DEFAULT CURRENT_TIMESTAMP, 'updated_at' DATETIME, 'connected_at' DATETIME, 'status' TEXT DEFAULT 'idle')

CREATE TABLE 'connection' ('id' TEXT PRIMARY KEY NOT NULL, 'session_id' TEXT NOT NULL, 'type' TEXT, 'secret' TEXT NOT NULL, 'name' TEXT NOT NULL, 'path' TEXT NOT NULL, 'created_at' DATETIME DEFAULT CURRENT_TIMESTAMP, 'connected_at' DATETIME, 'status' TEXT DEFAULT 'idle' )
