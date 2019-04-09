package com.tony.utils;

import java.util.ArrayList;
import java.util.List;
import com.mongodb.MongoClient;
import com.mongodb.MongoCredential;
import com.mongodb.ServerAddress;
import com.mongodb.client.MongoDatabase;

public class MongoDBUtil {

    public static MongoDatabase mongoDatabase;

    static {
        System.err.println("创建了一个MongoDB连接");
        mongoDatabase = createMongoDBConnection();
    }

    private  static MongoDatabase createMongoDBConnection() {
        try {
            ServerAddress serverAddress = new ServerAddress(
                    PropertiesUtil.getValue("mongoHost"),
                    Integer.valueOf(PropertiesUtil.getValue("mongoPort")));
            ServerAddress serverAddress2 = new ServerAddress(
                    PropertiesUtil.getValue("mongoHost2"),
                    Integer.valueOf(PropertiesUtil.getValue("mongoPort2")));
            List<ServerAddress> addrs = new ArrayList<ServerAddress>();
            addrs.add(serverAddress);
            addrs.add(serverAddress2);

            MongoCredential credential = MongoCredential.createScramSha1Credential(
                    PropertiesUtil.getValue("mongoUsername"), PropertiesUtil.getValue("mongoDatabase"),
                    PropertiesUtil.getValue("mongoPwd").toCharArray());
            List<MongoCredential> credentials = new ArrayList<>();
            credentials.add(credential);

            @SuppressWarnings("resource")
//            MongoClient mongoClient = new MongoClient(addrs,credentials);
            MongoClient mongoClient = new MongoClient(addrs);
            return mongoClient.getDatabase( PropertiesUtil.getValue("mongoDatabase"));

        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }
}
