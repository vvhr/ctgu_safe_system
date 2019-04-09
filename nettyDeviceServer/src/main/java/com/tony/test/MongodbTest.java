package com.tony.test;

import com.mongodb.client.MongoCollection;
import com.tony.utils.MongoDBUtil;
import org.bson.Document;

public class MongodbTest {
    public static void main(String[] arg){
        System.out.println("test mongodb");
        MongoCollection<Document> collection = MongoDBUtil.mongoDatabase.getCollection("reportRealTimeOneCollection");
        Document doc = new Document();
        doc.put("name","tony");
        doc.put("age","36");
        collection.insertOne(doc);
    }
}
